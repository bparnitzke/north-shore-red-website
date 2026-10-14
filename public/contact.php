<?php
/**
 * North Shore Red contact form handler.
 *
 * The handler records the consent decision before sending email. It never
 * sends an SMS; a messaging provider must be integrated separately.
 */

declare(strict_types=1);

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Location: /contact/', true, 302);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

function respond(bool $ok, string $message = '', int $status = 422): never
{
    http_response_code($ok ? 200 : $status);
    echo json_encode(['ok' => $ok, 'message' => $message]);
    exit;
}

function clean_header_value(string $value): string
{
    return trim(str_replace(["\r", "\n"], '', $value));
}

function uuid_v4(): string
{
    $data = random_bytes(16);
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function load_consent_config(string $filename, string $label): array
{
    $path = __DIR__ . '/config/' . $filename;
    if (!is_readable($path)) {
        throw new RuntimeException($label . ' consent configuration is unavailable.');
    }

    $config = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    foreach (['version', 'programId', 'exactText'] as $key) {
        if (empty($config[$key]) || !is_string($config[$key])) {
            throw new RuntimeException($label . ' consent configuration is invalid.');
        }
    }
    return $config;
}

$honeypot = trim((string) ($_POST['honeypot'] ?? ''));
if ($honeypot !== '') {
    respond(true);
}

$renderedAt = (int) ($_POST['rendered_at'] ?? 0);
if ($renderedAt > 0 && (time() - intdiv($renderedAt, 1000)) < 2) {
    respond(true);
}

$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$comments = trim((string) ($_POST['comments'] ?? ''));
$phone = trim((string) ($_POST['phone'] ?? ''));
$smsConsent = !empty($_POST['sms_consent']);
$emailConsent = !empty($_POST['email_consent']);
$formType = trim((string) ($_POST['form_type'] ?? 'contact'));
$earlyVoteMethod = trim((string) ($_POST['early_vote_method'] ?? ''));
$earlyVoteCommitment = !empty($_POST['early_vote_commitment']);
$sourceUrl = substr(trim((string) ($_POST['source_url'] ?? '')), 0, 2048);

if ($name === '') {
    respond(false, 'Please fill in the required fields.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(false, 'Please enter a valid email address.');
}

if (!in_array($formType, ['contact', 'early_vote_commitment'], true)) {
    respond(false, 'Please refresh the form and try again.');
}

$phoneDigits = preg_replace('/\D+/', '', $phone) ?? '';
if ($phone !== '' && (strlen($phoneDigits) < 10 || strlen($phoneDigits) > 15)) {
    respond(false, 'Please enter a valid mobile number.');
}

if ($smsConsent) {
    if ($phone === '') {
        respond(false, 'Please enter a valid mobile number.');
    }
}

$allowedVoteMethods = ['early_in_person', 'absentee_by_mail', 'unsure'];
if ($formType === 'early_vote_commitment') {
    if (!$earlyVoteCommitment || !in_array($earlyVoteMethod, $allowedVoteMethods, true)) {
        respond(false, 'Please complete your early-vote commitment.');
    }
} else {
    $earlyVoteMethod = '';
    $earlyVoteCommitment = false;
}

$interestLabels = [
    'list' => 'Join the general mailing list',
    'event' => 'Join the fundraiser invitation list',
    'volunteer' => 'Sign up to volunteer'
];
$interestsRaw = $_POST['interest'] ?? [];
if (!is_array($interestsRaw)) {
    $interestsRaw = [$interestsRaw];
}
$interestKeys = array_values(array_intersect(array_keys($interestLabels), $interestsRaw));
$interests = array_map(static fn(string $key): string => $interestLabels[$key], $interestKeys);

try {
    $smsConfig = load_consent_config('sms-consent.json', 'SMS');
    $emailConfig = load_consent_config('email-consent.json', 'Email');
} catch (Throwable $error) {
    error_log('North Shore Red consent configuration is unavailable.');
    respond(false, 'We could not securely record your request. Please try again later.', 503);
}

$postedVersion = trim((string) ($_POST['sms_consent_version'] ?? ''));
$postedProgram = trim((string) ($_POST['sms_program_id'] ?? ''));
$postedEmailVersion = trim((string) ($_POST['email_consent_version'] ?? ''));
$postedEmailProgram = trim((string) ($_POST['email_program_id'] ?? ''));
if (
    $postedVersion !== $smsConfig['version']
    || $postedProgram !== $smsConfig['programId']
    || $postedEmailVersion !== $emailConfig['version']
    || $postedEmailProgram !== $emailConfig['programId']
) {
    respond(false, 'The form was updated while this page was open. Please refresh and try again.', 409);
}

$dsn = getenv('NSR_DB_DSN') ?: '';
$dbUser = getenv('NSR_DB_USER') ?: '';
$dbPassword = getenv('NSR_DB_PASSWORD') ?: '';
if ($dsn === '' || $dbUser === '') {
    respond(false, 'We could not securely record your request. Please try again later.', 503);
}

$submissionId = uuid_v4();
$submittedAt = (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s.u');
$submissionMetadata = [
    'form_type' => $formType,
    'early_vote_commitment' => $earlyVoteCommitment,
    'early_vote_method' => $earlyVoteMethod !== '' ? $earlyVoteMethod : null
];

try {
    $pdo = new PDO($dsn, $dbUser, $dbPassword, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
    $pdo->beginTransaction();

    $receipt = $pdo->prepare(
        'INSERT INTO contact_submission_receipts
        (id, submitted_at, source_url, form_type, interests_json, submission_metadata_json,
         email_consent, sms_consent)
        VALUES (:id, :submitted_at, :source_url, :form_type, :interests_json, :submission_metadata_json,
                :email_consent, :sms_consent)'
    );
    $receipt->execute([
        ':id' => $submissionId,
        ':submitted_at' => $submittedAt,
        ':source_url' => $sourceUrl,
        ':form_type' => $formType,
        ':interests_json' => json_encode($interestKeys, JSON_THROW_ON_ERROR),
        ':submission_metadata_json' => json_encode($submissionMetadata, JSON_THROW_ON_ERROR),
        ':email_consent' => $emailConsent ? 1 : 0,
        ':sms_consent' => $smsConsent ? 1 : 0
    ]);

    $event = $pdo->prepare(
        'INSERT INTO sms_consent_events
        (contact_submission_id, event_type, event_at, phone, source_url, program_id,
         consent_language_version, consent_language_text, metadata_json)
        VALUES (:contact_submission_id, :event_type, :event_at, :phone, :source_url, :program_id,
                :consent_language_version, :consent_language_text, :metadata_json)'
    );
    $event->execute([
        ':contact_submission_id' => $submissionId,
        ':event_type' => $smsConsent ? 'opt_in' : 'declined',
        ':event_at' => $submittedAt,
        ':phone' => $smsConsent ? $phone : null,
        ':source_url' => $sourceUrl,
        ':program_id' => $smsConfig['programId'],
        ':consent_language_version' => $smsConfig['version'],
        ':consent_language_text' => $smsConfig['exactText'],
        ':metadata_json' => json_encode([
            'interests' => $interestKeys,
            'form_type' => $formType,
            'early_vote_method' => $earlyVoteMethod !== '' ? $earlyVoteMethod : null
        ], JSON_THROW_ON_ERROR)
    ]);

    $emailEvent = $pdo->prepare(
        'INSERT INTO email_consent_events
        (contact_submission_id, event_type, event_at, email, source_url, program_id,
         consent_language_version, consent_language_text, metadata_json)
        VALUES (:contact_submission_id, :event_type, :event_at, :email, :source_url, :program_id,
                :consent_language_version, :consent_language_text, :metadata_json)'
    );
    $emailEvent->execute([
        ':contact_submission_id' => $submissionId,
        ':event_type' => $emailConsent ? 'opt_in' : 'declined',
        ':event_at' => $submittedAt,
        ':email' => $emailConsent ? $email : null,
        ':source_url' => $sourceUrl,
        ':program_id' => $emailConfig['programId'],
        ':consent_language_version' => $emailConfig['version'],
        ':consent_language_text' => $emailConfig['exactText'],
        ':metadata_json' => json_encode([
            'interests' => $interestKeys,
            'form_type' => $formType,
            'early_vote_method' => $earlyVoteMethod !== '' ? $earlyVoteMethod : null
        ], JSON_THROW_ON_ERROR)
    ]);

    $pdo->commit();
} catch (Throwable $error) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('North Shore Red consent ledger write failed.');
    respond(false, 'We could not securely record your request. Please try again later.', 503);
}

$recipients = ['brian@orbiterstrategies.com', 'NorthShoreRedFund@gmail.com'];
$subjectInterests = $formType === 'early_vote_commitment'
    ? 'Early vote commitment'
    : ($interests === [] ? 'General message' : implode(', ', $interests));
$subject = '[North Shore Red contact] ' . $subjectInterests . ' — ' . $name;
$body = implode("\n", [
    'New message from the North Shore Red contact form.',
    'Consent record: ' . $submissionId,
    '',
    'Form: ' . $formType,
    'Interests: ' . ($interests === [] ? 'None selected' : implode(', ', $interests)),
    'Early-vote commitment: ' . ($earlyVoteCommitment ? 'Yes' : 'No'),
    'Early-vote method: ' . ($earlyVoteMethod !== '' ? $earlyVoteMethod : 'Not applicable'),
    '',
    'Name: ' . $name,
    'Email: ' . $email,
    'Phone: ' . ($phone !== '' ? $phone : 'Not provided'),
    'Email updates consent: ' . ($emailConsent ? 'Yes' : 'No'),
    'Email program: ' . $emailConfig['programId'],
    'Email disclosure version: ' . $emailConfig['version'],
    'SMS consent: ' . ($smsConsent ? 'Yes' : 'No'),
    'SMS program: ' . $smsConfig['programId'],
    'Disclosure version: ' . $smsConfig['version'],
    '',
    'Comments:',
    $comments !== '' ? $comments : 'None provided'
]);

$fromHost = preg_replace('/[^a-z0-9.-]/i', '', (string) ($_SERVER['HTTP_HOST'] ?? 'northshorered.com'));
$fromAddress = 'no-reply@' . preg_replace('/^www\./', '', $fromHost);
$headers = [
    'From: North Shore Red Website <' . $fromAddress . '>',
    'Reply-To: ' . clean_header_value($name) . ' <' . clean_header_value($email) . '>',
    'Content-Type: text/plain; charset=utf-8'
];

$sent = @mail(implode(', ', $recipients), $subject, $body, implode("\r\n", $headers));
if (!$sent) {
    respond(false, 'Your request was recorded, but the email could not be sent. Please try again in a moment.', 503);
}

respond(true);
