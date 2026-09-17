<?php
/**
 * North Shore Red contact form handler.
 *
 * Astro's static build copies this file into the deployed public_html
 * unchanged. It receives the contact form's POST, validates it, and emails
 * both recipients with the submitter set as Reply-To.
 */

declare(strict_types=1);

// A GET request here is almost always someone following an old bookmark to
// the previous site's contact.php page — send them to the real page instead
// of showing an empty API response.
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Location: /contact/', true, 302);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

function respond(bool $ok, string $message = ''): never
{
    http_response_code($ok ? 200 : 422);
    echo json_encode(['ok' => $ok, 'message' => $message]);
    exit;
}

// Strip anything that could be used for header/CRLF injection.
function clean_header_value(string $value): string
{
    return trim(str_replace(["\r", "\n"], '', $value));
}

$honeypot = trim((string) ($_POST['honeypot'] ?? ''));
if ($honeypot !== '') {
    // Bots fill hidden fields. Pretend success so they don't learn why it failed.
    respond(true);
}

// Lightweight timing check: a human takes at least a couple of seconds to
// fill this out. Pairs with the honeypot as the "real spam check" called for
// in the design handoff, without adding a CAPTCHA dependency.
$renderedAt = (int) ($_POST['rendered_at'] ?? 0);
if ($renderedAt > 0 && (time() - intdiv($renderedAt, 1000)) < 2) {
    respond(true);
}

$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$comments = trim((string) ($_POST['comments'] ?? ''));

if ($name === '' || $comments === '') {
    respond(false, 'Please fill in the required fields.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(false, 'Please enter a valid email address.');
}

$address = trim((string) ($_POST['address'] ?? ''));
$address2 = trim((string) ($_POST['address2'] ?? ''));
$city = trim((string) ($_POST['city'] ?? ''));
$state = trim((string) ($_POST['state'] ?? ''));
$zip = trim((string) ($_POST['zip'] ?? ''));
$phone = trim((string) ($_POST['phone'] ?? ''));
$smsConsent = !empty($_POST['sms_consent']);

$interestLabels = [
    'list' => 'Join the general mailing list',
    'event' => 'Join the fundraiser invitation list',
    'volunteer' => 'Sign up to volunteer'
];
$interestsRaw = $_POST['interest'] ?? [];
if (!is_array($interestsRaw)) {
    $interestsRaw = [$interestsRaw];
}
$interests = array_values(array_intersect_key($interestLabels, array_flip($interestsRaw)));

$recipients = ['brian@orbiterstrategies.com', 'NorthShoreRedFund@gmail.com'];

$subjectInterests = $interests === [] ? 'General message' : implode(', ', $interests);
$subject = '[North Shore Red contact] ' . $subjectInterests . ' — ' . $name;

$bodyLines = [
    'New message from the North Shore Red contact form.',
    '',
    'Interests: ' . ($interests === [] ? 'None selected' : implode(', ', $interests)),
    '',
    'Name: ' . $name,
    'Email: ' . $email,
    'Phone: ' . ($phone !== '' ? $phone : 'Not provided'),
    'SMS consent: ' . ($smsConsent ? 'Yes' : 'No'),
    'Address: ' . implode(' ', array_filter([$address, $address2])),
    'City/State/Zip: ' . implode(', ', array_filter([$city, $state, $zip])),
    '',
    'Comments:',
    $comments
];
$body = implode("\n", $bodyLines);

$fromAddress = 'no-reply@' . preg_replace('/^www\./', '', (string) ($_SERVER['HTTP_HOST'] ?? 'northshorered.com'));

$headers = [
    'From: North Shore Red Website <' . $fromAddress . '>',
    'Reply-To: ' . clean_header_value($name) . ' <' . clean_header_value($email) . '>',
    'Content-Type: text/plain; charset=utf-8'
];

$sent = @mail(implode(', ', $recipients), $subject, $body, implode("\r\n", $headers));

if (!$sent) {
    respond(false, 'Something went wrong sending your message. Please try again in a moment.');
}

respond(true);
