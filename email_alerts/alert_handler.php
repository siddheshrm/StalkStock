<?php
include __DIR__ . '/../connection.php';

use PHPMailer\PHPMailer\PHPMailer;

require __DIR__ . '/../PHPMailer/src/Exception.php';
require __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../PHPMailer/src/SMTP.php';

function trigger_email_alerts($alerts)
{
    // Group alerts by user
    $groupedAlerts = [];
    foreach ($alerts as $alert) {
        $email = $alert['email'];
        $groupedAlerts[$email]['name'] = $alert['name'];
        $groupedAlerts[$email]['products'][] = [
            'title' => $alert['product_title'],
            'url' => $alert['url'],
        ];
    }

    // Send consolidated email to each user
    foreach ($groupedAlerts as $email => $userAlerts) {
        $userName = $userAlerts['name'];
        $products = $userAlerts['products'];

        // Prepare the consolidated email content
        $subject = "Products Back in Stock!";
        $message = generate_email_content($userName, $products);

        // Send the email
        if (send_email($email, $subject, $message)) {
            write_log("Consolidated alert sent to $email.");
        } else {
            write_log("Failed to send alert to $email.");
        }
    }
}

function generate_email_content($userName, $products)
{
    $productList = '';
    foreach ($products as $product) {
        $productList .= "
        <tr>
            <td style='padding: 8px; border: 1px solid #ddd;'>{$product['title']}</td>
            <td style='padding: 8px; border: 1px solid #ddd;'>
                <a href='{$product['url']}' target='_blank'>🔗 View Product</a>
            </td>
        </tr>";
    }

    return "
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
                .footer {
                    background-color: #ffe985;
                    text-align: center;
                    padding: 10px;
                    font-size: 0.9rem;
                    font-weight: 400;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 20px;
                }
                table th, table td {
                    text-align: left;
                    padding: 8px;
                    border: 1px solid #ddd;
                }
                table th {
                    background-color: #ffdd47;
                    color: #000;
                }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <div class='header'>
                    <h1>Hello $userName,</h1>
                    <p>The products you've been tracking are back in stock! 🙌</p>
                </div>
                <div class='content'>
                    <table>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Link</th>
                            </tr>
                        </thead>
                        <tbody>
                            $productList
                        </tbody>
                    </table>
                    <p><b>Cheers,<br>Team StalkStock</b></p>
                </div>
                <div class='footer'>
                    &copy; 2024 StalkStock. All rights reserved.
                </div>
            </div>
        </body>
        </html>
    ";
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
        write_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
