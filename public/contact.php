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

function load_sms_config(): array
{
    $path = __DIR__ . '/config/sms-consent.json';
    if (!is_readable($path)) {
        throw new RuntimeException('SMS consent configuration is unavailable.');
    }

    $config = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    foreach (['version', 'programId', 'exactText'] as $key) {
        if (empty($config[$key]) || !is_string($config[$key])) {
            throw new RuntimeException('SMS consent configuration is invalid.');
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
$sourceUrl = substr(trim((string) ($_POST['source_url'] ?? '')), 0, 2048);

if ($name === '' || $comments === '') {
    respond(false, 'Please fill in the required fields.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(false, 'Please enter a valid email address.');
}

if ($smsConsent) {
    $phoneDigits = preg_replace('/\D+/', '', $phone) ?? '';
    if (strlen($phoneDigits) < 10 || strlen($phoneDigits) > 15) {
        respond(false, 'Please enter a valid mobile number.');
    }
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
    $smsConfig = load_sms_config();
} catch (Throwable $error) {
    error_log('North Shore Red SMS consent configuration is unavailable.');
    respond(false, 'We could not securely record your request. Please try again later.', 503);
}

$postedVersion = trim((string) ($_POST['sms_consent_version'] ?? ''));
$postedProgram = trim((string) ($_POST['sms_program_id'] ?? ''));
if ($postedVersion !== $smsConfig['version'] || $postedProgram !== $smsConfig['programId']) {
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

try {
    $pdo = new PDO($dsn, $dbUser, $dbPassword, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
    $pdo->beginTransaction();

    $receipt = $pdo->prepare(
        'INSERT INTO contact_submission_receipts
        (id, submitted_at, source_url, interests_json, sms_consent)
        VALUES (:id, :submitted_at, :source_url, :interests_json, :sms_consent)'
    );
    $receipt->execute([
        ':id' => $submissionId,
        ':submitted_at' => $submittedAt,
        ':source_url' => $sourceUrl,
        ':interests_json' => json_encode($interestKeys, JSON_THROW_ON_ERROR),
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
        ':metadata_json' => json_encode(['interests' => $interestKeys], JSON_THROW_ON_ERROR)
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
$subjectInterests = $interests === [] ? 'General message' : implode(', ', $interests);
$subject = '[North Shore Red contact] ' . $subjectInterests . ' — ' . $name;
$body = implode("\n", [
    'New message from the North Shore Red contact form.',
    'Consent record: ' . $submissionId,
    '',
    'Interests: ' . ($interests === [] ? 'None selected' : implode(', ', $interests)),
    '',
    'Name: ' . $name,
    'Email: ' . $email,
    'Phone: ' . ($phone !== '' ? $phone : 'Not provided'),
    'SMS consent: ' . ($smsConsent ? 'Yes' : 'No'),
    'SMS program: ' . $smsConfig['programId'],
    'Disclosure version: ' . $smsConfig['version'],
    '',
    'Comments:',
    $comments
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
