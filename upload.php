<?php
session_set_cookie_params(0, '/', '', true, true);
ini_set('session.cookie_samesite', 'Lax');
session_start();

require_once 'config/.config.php';
include_once 'lib/cloud_upload.php';
include_once 'lib/services.php';
include_once 'lib/access_token.php';
include_once 'lib/validate_captcha.php';


// Check if the form was submitted
$message = "";
$name = "";
$email = "";
$upload_status = false;

$csrfToken = generateCsrfToken(AppConfig::SECURITY["CSRF_SECRET"]);
$_SESSION['csrf_token'] = $csrfToken;

$token = isset($_GET['token']) ? sanitize_vars($_GET['token']) : '';

$valid_token = verify_access_token_redis($token);


// POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = isset($_POST['from_action']) ? sanitize_vars($_POST['from_action']) : '';

    // Retrieve CSRF token from the form and session
    $csrfToken = isset($_POST['csrf_token']) ? sanitize_vars($_POST['csrf_token']) : '';
    if (!validateCsrfToken($csrfToken, AppConfig::SECURITY["CSRF_SECRET"])) {
        // Invalid CSRF token
        $message = "Invalid security token.";
        if ($action === 'registration') {
            $upload_status = false;
            include  'template/register.php';
            exit;
        }
        echo json_encode([
            "message" =>  $message,
            "error" => True
        ]);
        exit;
    }

    // Retrieve the name, email, and image file from the form
    // Sanitize input data
    $name = isset($_POST['name']) ? sanitize_vars($_POST['name']) : '';
    $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL) : '';

    if ($action === 'registration') {
        $captcha_response = isset($_POST['h-captcha-response']) ? $_POST['h-captcha-response'] : '';
        if (!validate_captcha($captcha_response)) {
            $upload_status = false;
            $message =  "Error of captcha validate. Please try again.";
            include  'template/register.php';
            exit;
        }
        $success = (bool) ($email && send_token_to_user($email));
        $upload_status = $success;
        $message = date("Y-m-d H:i:s") . " UTC.  " . ($success ? "Success." : "Error sending email. Please try again.");
        // LOAD TEMPLATE
        include $success ?  "template/email_sent.php" : 'template/register.php';
        exit;
    } else { 
        //action === 'upload'
        if (!$valid_token) {
            echo json_encode([
                "message" => "Invalid access token. Please register email for a new access token.",
                "error" => True,
                "access_token" => false
            ]);
            exit;
        }

        $image = $_FILES['image'] ?? null;
        $message_error = false;

        // Validate the file size
        if (isset($image)) {
            // Get the size of the uploaded file
            $fileSize = $image['size'];
            // Check if the file size exceeds the limit
            if ($fileSize > AppConfig::MAX_FILE_SIZE) {
                echo json_encode([
                    "message" => "File size exceeds the maximum limit of" . AppConfig::MAX_FILE_SIZE / 1024 / 1024 . "MB.",
                    "error" => True
                ]);
                exit;
            }
        }

        // Validate the file upload
        if ($image && $image['error']  === UPLOAD_ERR_OK) {
            $target_file =  basename($image['name']);

            list($upload_status, $upload_timestamp) = cloud_upload($image['tmp_name'], $image['name']);
            if ($upload_status) {
                $email = $valid_token;
                $example = [
                    'name' =>  $name,
                    'email' => $email,
                    'original_name' => $image['name'],
                    'timestamp' =>  format_timestamp($upload_timestamp)
                ];
                $virtualFile = create_virtual_file($example);
                list($upload_status, $upload_timestamp) = cloud_upload($virtualFile, ".txt", $upload_timestamp);
            }
            $result_text = $upload_status ? "was uploaded successfully" : "failed to upload";
            $message = "The file \"" . $image['name'] . "\" " . $result_text .
                ". Timestamp: " . format_timestamp($upload_timestamp) . ".";
            $message_error = !$upload_status;
        } else {
            $message = "Sorry, there was an error with your file upload.";
            $message_error = true;
        }
        echo json_encode(["message" => $message, "error" => $message_error]);
        exit;
    } // action
}

// POST and GET
if (isset($_GET['token']) && !$valid_token) {
    $message = "Invalid access token. Please register email for a new access token.";
    $upload_status = false;
}

// LOAD TEMPLATE
$email = $valid_token;
include  $valid_token ? 'template/upload.php' : 'template/register.php';


