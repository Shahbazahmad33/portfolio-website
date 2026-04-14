<?php
$receiving_email_address = 'shahbaz.swe@gmail.com';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo 'Method not allowed.';
  exit();
}

$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

if ($name === '' || $email === '' || $subject === '' || $message === '') {
  echo 'All fields are required.';
  exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  echo 'Please enter a valid email address.';
  exit();
}

// Basic header injection prevention.
$injection_pattern = "/(content-type|bcc:|cc:|to:|mime-version|multipart\\/mixed|content-transfer-encoding)/i";
foreach ([$name, $email, $subject] as $value) {
  if (preg_match($injection_pattern, $value)) {
    echo 'Invalid form input detected.';
    exit();
  }
}

$safe_name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
$safe_email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
$safe_subject = 'Portfolio Contact: ' . strip_tags($subject);
$safe_message = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));

$email_body = "
  <h2>New message from shahbazahmad.com</h2>
  <p><strong>Name:</strong> {$safe_name}</p>
  <p><strong>Email:</strong> {$safe_email}</p>
  <p><strong>Subject:</strong> " . htmlspecialchars($subject, ENT_QUOTES, 'UTF-8') . "</p>
  <p><strong>Message:</strong><br>{$safe_message}</p>
";

$headers = 'From: Website Contact Form <no-reply@shahbazahmad.com>' . "\r\n" .
  'Reply-To: ' . $email . "\r\n" .
  'MIME-Version: 1.0' . "\r\n" .
  'Content-Type: text/html; charset=UTF-8' . "\r\n" .
  'X-Mailer: PHP/' . phpversion();

$sent = mail($receiving_email_address, $safe_subject, $email_body, $headers);

if ($sent) {
  echo 'OK';
  exit();
}

echo 'Unable to send right now. Please email directly at shahbaz.swe@gmail.com.';
?>
