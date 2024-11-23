<?php
include __DIR__ . '/../connection.php';

use PHPMailer\PHPMailer\PHPMailer;

require __DIR__ . '/../PHPMailer/src/Exception.php';
require __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../PHPMailer/src/SMTP.php';

function trigger_email_alerts($alerts)
{
    foreach ($alerts as $alert) {
        $email = $alert['email'];
        $productTitle = $alert['product_title'];
        $url = $alert['url'];
        $userName = $alert['name'];

        $subject = "Product Available: " . $productTitle;
        $message = "Good news, $userName 🎉\n\n";
        $message .= "The product you've been waiting for is finally in stock! 🙌\n\n";
        $message .= "🔗 Check it out here: $url\n\n";
        $message .= "Cheers,\n";
        $message .= "Team StalkStock";

        // Send the email
        if (send_email($email, $subject, $message)) {
            echo "Alert sent to $email for $productTitle.\n";
        } else {
            echo "Failed to send alert to $email for $productTitle.\n";
        }
    }
}

function send_email($recepient, $subject, $body)
{
    $mail = new PHPMailer(true);

    try {
        // Send password recovery email
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'email@gmail.com';
        $mail->Password = 'abcd efgh ijkl mnop';    // Note: This is a placeholder password for demonstration purposes.
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        // Recipients
        $mail->setFrom('email@example.com', 'Product Alerts');
        $mail->addAddress($recepient);

        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}\n";
        return false;
    }
}
