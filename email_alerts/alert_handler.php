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
        $message = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body {
                    font-family: Verdana, sans-serif;
                    background-color: #fff8d6;
                    margin: 0;
                    padding: 0;
                }
                .email-container {
                    max-width: 800px;
                    margin: 20px auto;
                    background: #fff0ad;
                    border-radius: 8px;
                    overflow: hidden;
                    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                }
                .header {
                    background-color: #ffe985;
                    padding: 20px;
                    text-align: center;
                }
                .header h1 {
                    margin: 0;
                    font-size: 1.8rem;
                }
                .content {
                    padding: 20px;
                    color: #333333;
                }
                .content p {
                    margin: 10px 0;
                    line-height: 1.6;
                }
                .content a {
                    display: inline-block;
                    padding: 10px 20px;
                    margin: 20px 0;
                    background-color: #ffdd47;
                    color: #000;
                    text-decoration: none;
                    border-radius: 4px;
                    font-weight: 500;
                }
                .content a:hover {
                    background-color: #ffd61f;
                    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
                }
                .footer {
                    background-color: #ffe985;
                    text-align: center;
                    padding: 10px;
                    font-size: 0.9rem;
                    font-weight: 400;
                }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <div class='header'>
                    <h1>Good News, $userName 🎉</h1>
                </div>
                <div class='content'>
                    <p>The product you've been waiting for is finally in stock! 🙌</p>
                    <p><a href='$url' target='_blank'>🔗 Check it out here</a></p>
                    <p><b>Cheers,<br>Team StalkStock</b></p>
                </div>
                <div class='footer'>
                    &copy; 2024 StalkStock. All rights reserved.
                </div>
            </div>
        </body>
        </html>";

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
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'email@gmail.com';
        $mail->Password = 'abcd efgh ijkl mnop';    // Note: This is a placeholder password for demonstration purposes.
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('email@gmail.com', 'StalkStock');
        $mail->addAddress($recepient);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}\n";
        return false;
    }
}
