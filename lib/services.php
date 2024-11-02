<?php

use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


// Function to format timestamp
function format_timestamp($timestamp): string
{
    $date = DateTime::createFromFormat('Ymd-His', $timestamp);
    $date->setTimezone(new DateTimeZone('UTC'));
    $formattedDate = $date->format('Y-m-d H:i:s \U\T\C');
    return $formattedDate;
}


/**
 * Send an email to $to with a link containing a token that can be used for one hour to upload files.
 * If $token is not provided, generate a new one.
 * The link will be $full_url?token=$token.
 * The email body will be a simple html message with the link.
 * If the email cannot be sent, log the error and return false.
 * If the token cannot be stored, log the error and return false.
 * Return true on success.
 *
 * @param string $to The email address to send the email to
 * @param string $subject The subject of the email
 * @param string $message The body of the email
 * @param bool $is_html Whether the email body is HTML (default false)
 * @return bool True if the email was sent successfully, false otherwise
 */
function sendEmail($to, $subject, $message, $is_html = false)
{
    // Autoload Composer dependencies
    require_once 'vendor/autoload.php';

    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = AppConfig::EMAIL_SMTP["HOST"];
        $mail->SMTPAuth = true;
        $mail->Username = AppConfig::EMAIL_SMTP["USERNAME"];
        $mail->Password = AppConfig::EMAIL_SMTP["PASSWORD"];
        $mail->SMTPSecure = AppConfig::EMAIL_SMTP["ENCRYPTION"] === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = AppConfig::EMAIL_SMTP["PORT"];
        $mail->XMailer = 'Service of upload files';

        $mail->setFrom(AppConfig::EMAIL_SMTP["FROM_EMAIL"], AppConfig::EMAIL_SMTP["FROM_NAME"]);
        $mail->addAddress($to);
        $mail->addReplyTo(AppConfig::EMAIL_SMTP["REPLYTO_EMAIL"], "Don't reply to this email");

        $mail->isHTML($is_html);
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->AltBody = strip_tags($message);

        if (!$mail->send()) {
            $message_error = "Mailer Error: " . $mail->ErrorInfo;
            return $message_error;
        }
    } catch (Exception $e) {
        $message_error = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        return $message_error;
    }
    return false;
}


/**
 * Send an email to $to with a link containing a token that can be used for one hour to upload files.
 * If $token is not provided, generate a new one.
 * The link will be $full_url?token=$token.
 * The email body will be a simple html message with the link.
 * If the email cannot be sent, log the error and return false.
 * If the token cannot be stored, log the error and return false.
 * Return true on success.
 */
function send_token_to_user($to, $token = null): bool
{
    if ($token === null) {
        $token = generateToken($to);
    }
    $store_result = store_access_token_redis($token, $to);
    if (!$store_result) {
        error_log("Error store_access_token_redis: " . $to, 0);
        return false;
    }

    // Construct the full URL with the token
    $full_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . strtok($_SERVER['REQUEST_URI'], '?');
    $link = $full_url . '?' . 'token=' . $token;
    $app_name = AppConfig::APP_NAME;
    $message = include 'template/email_template.php';
    $subject = "$app_name Temporary Dropbox Link";
    $error = sendEmail($to, $subject, $message, true);
    if ($error) {
        error_log("Error sending email: " . $error, 0);
        return false;
    }
    return (bool)!$error;
}


function sanitize_vars($var)
{
    return filter_var(trim($var), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
}
