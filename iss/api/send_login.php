<?php

ini_set('log_errors', 'On');
ini_set('error_log', __DIR__ . '/error_log.txt');
error_reporting(E_ALL);

header("Content-Type: application/json");
require 'PHPMailer/PHPMailer/src/PHPMailer.php';
require 'PHPMailer/PHPMailer/src/SMTP.php';
require 'PHPMailer/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include '../controls/credentials.php';

function sendLoginCodeEmail($email, $code) {
    global $email_credentials;
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $email_credentials['email_username'];
        $mail->Password = $email_credentials['email_password'];
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->setFrom($email_credentials['email_username']);
        $mail->addAddress($email); 
        $mail->isHTML(true);
        $mail->Subject = 'Your Login Code';
        $mail->Body = "
            <p>Your login code is:</p>
            <h2>{$code}</h2>
            <p>This code is valid for one-time use only.</p>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

function handleRequest() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['email'])) {
            echo json_encode(['success' => false, 'message' => 'Email is required', "data" => null]);
            return;
        }

        $email = $data['email'];
        $code = random_int(100000, 999999); // Generate a 6-digit login code

        $email_sent = sendLoginCodeEmail($email, $code);

        if ($email_sent) {
            // Code is returned for local storage in the frontend (for development/debugging only)
            echo json_encode(['success' => true, 'message' => 'Login code sent to your email', 'code' => $code]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to send email', "data" => null]);
        }
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Invalid request method', "data" => null]);
    }
}

handleRequest();
