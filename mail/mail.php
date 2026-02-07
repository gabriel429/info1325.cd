<?php
// Simple contact mail handler with optional PHPMailer/SMTP support
require_once __DIR__ . '/../configUrl.php';

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$subject = trim($_POST['subject'] ?? 'Message via site');
$message = trim($_POST['message'] ?? '');

$to = 'projet1325@gmail.com';
$full_subject = 'Nouveau message site: ' . ($subject ?: 'Contact');
$email_message = "Name: " . $name . "\nEmail: " . $email . "\nPhone: " . $phone . "\nSubject: " . $subject . "\n\nMessage:\n" . $message . "\n";
$headers = "From: " . ($email ?: 'no-reply@info1325.cd') . "\r\n";

$sent = false;

// Prefer SMTP via PHPMailer if available and smtp_config provided
$autoload = __DIR__ . '/../vendor/autoload.php';
$smtpCfg = __DIR__ . '/smtp_config.php';
if (file_exists($autoload) && file_exists($smtpCfg)) {
	require_once $autoload;
	require_once $smtpCfg;
	try {
		$mail = new PHPMailer\PHPMailer\PHPMailer(true);
		$mail->isSMTP();
		$mail->Host = SMTP_HOST;
		$mail->SMTPAuth = true;
		$mail->Username = SMTP_USER;
		$mail->Password = SMTP_PASS;
		$mail->SMTPSecure = SMTP_SECURE;
		$mail->Port = SMTP_PORT;
		if (defined('SMTP_OPTIONS') && is_array(SMTP_OPTIONS)) {
			// e.g. for local dev allow self-signed
			$mail->SMTPOptions = SMTP_OPTIONS;
		}
		$mail->setFrom(SMTP_USER, 'Site R1325');
		$mail->addAddress($to);
		if ($email) $mail->addReplyTo($email, $name ?: $email);
		$mail->Subject = $full_subject;
		$mail->Body = $email_message;
			$mail->send();
			$sent = true;
	} catch (Exception $e) {
			$sent = false;
			// Log PHPMailer exception for debugging
			$logDir = __DIR__ . '/logs';
			if (!is_dir($logDir)) @mkdir($logDir, 0755, true);
			$logFile = $logDir . '/failed_emails.log';
			$entry = "-----\n" . date('c') . "\nPHPMailer error: " . $e->getMessage() . "\nHost: " . SMTP_HOST . ":" . SMTP_PORT . "\nUser: " . SMTP_USER . "\nTo: " . $to . "\nSubject: " . $full_subject . "\n\n" . $email_message . "\n";
			@file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
	}
} else {
	// fallback to PHP mail()
	$sent = mail($to, $full_subject, $email_message, $headers);
	if (!$sent) {
		// Log failed email for debugging
		$logDir = __DIR__ . '/logs';
		if (!is_dir($logDir)) @mkdir($logDir, 0755, true);
		$logFile = $logDir . '/failed_emails.log';
		$entry = "-----\n" . date('c') . "\nTo: $to\nSubject: $full_subject\nHeaders: $headers\n\n" . $email_message . "\n";
		@file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
	}
}

// Prepare WhatsApp prefilled message and redirect user to a redirect page that opens WhatsApp
$wa_text = rawurlencode("Message de: $name\nEmail: $email\nPhone: $phone\nSujet: $subject\n\n$message");
$wa_url = "https://wa.me/243821550225?text=" . $wa_text;

header('Location: ../mail/wa_redirect.php?wa=' . urlencode($wa_url) . '&sent=' . ($sent ? '1' : '0'));
exit;

?>
