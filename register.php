<?php
include 'connection.php';
echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>';

use PHPMailer\PHPMailer\PHPMailer;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function sendWelcomeEmail($email, $name)
{
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'stalkstock.emails@gmail.com';
    $mail->Password = 'kxrr fulo mmna bnjn';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    $mail->setFrom('stalkstock.emails@gmail.com', 'StalkStock');
    $mail->addAddress($email);

    // Email subject and body
    $mail->isHTML(true);
    $mail->Subject = 'Welcome to StalkStock!';
    $mail->Body = "
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
                font-size: 2rem;
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
                <h1>Welcome to StalkStock!</h1>
            </div>
            <div class='content'>
                <p><b>Hi $name,</b></p>
                <p>Welcome to <b>StalkStock</b>! We're excited to have you on board.</p>
                <p>To get started, simply log in and add your favorite products to track. You’ll be notified whenever
                    there’s an
                    update!</p>
                <p style='text-align: center;'>
                    <a href='http://localhost:8080/StalkStock/index.php'>
                        Log in here
                    </a>
                </p>
                <p>Thanks for choosing StalkStock!</p>
                <p><b>Cheers,<br>Team StalkStock</b></p>
            </div>
            <div class='footer'>
                &copy; 2024 StalkStock. All rights reserved.
            </div>
        </div>
    </body>

    </html>
    ";

    // Send the email
    if (!$mail->send()) {
        error_log("Error sending welcome email: " . $mail->ErrorInfo);
    }
}

// Registration Logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = strtolower($_POST['email']);
    $password = $_POST['password'];

    // Basic Validation
    if (strlen($name) < 5) {
        echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: "Invalid Name",
                text: "Your name must be at least 5 characters long. Please enter a valid name.",
                icon: "error",
                confirmButtonText: "OK"
            }).then(() => {
                window.location.href = "signup.php";
            });
        });
        </script>';
        exit();
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: "Invalid Email",
                text: "The email address you entered is invalid. Please check and enter a valid email",
                icon: "error",
                confirmButtonText: "OK"
            }).then(() => {
                window.location.href = "signup.php";
            });
        });
        </script>';
        exit();
    } elseif (strlen($password) < 8) {
        echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "Weak Password",
                        text: "Your password must be at least 8 characters long. Please enter a stronger password.",
                        icon: "error",
                        confirmButtonText: "OK"
                    }).then(() => {
                        window.location.href = "signup.php";
                    });
                });
              </script>';
        exit();
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id, is_guest FROM users WHERE LOWER(email) = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $is_guest);
            $stmt->fetch();

            // Check if is_guest is 0 (email already registered as a non-guest user)
            if ($is_guest == 0) {
                echo '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            title: "Email Already Registered",
                            text: "This email address is already registered. Please use a different email.",
                            icon: "error",
                            confirmButtonText: "OK"
                        }).then(() => {
                            window.location.href = "signup.php";
                        });
                    });
                </script>';
                exit();
            } else {
                // Hash the password before storing it
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                // Update the guest user to a normal user
                $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ?, is_guest = 0 WHERE id = ?");
                $stmt->bind_param("sssi", $name, $email, $hashed_password, $user_id);

                if ($stmt->execute()) {
                    // Send welcome email after registration
                    sendWelcomeEmail($email, $name);

                    // Redirect to login page with success message
                    echo '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            title: "Registration Complete!",
                            text: "Registration successful! Please login to continue.",
                            icon: "success",
                            confirmButtonText: "OK"
                        }).then(() => {
                            window.location.href = "index.php";
                        });
                    });
                  </script>';
                    exit();
                } else {
                    echo '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            title: "Registration Failed",
                            text: "There was an issue registering your account. Please try again later.",
                            icon: "error",
                            confirmButtonText: "OK"
                        }).then(() => {
                            window.location.href = "signup.php";
                        });
                    });
                  </script>';
                    exit();
                }
            }
        } else {
            // Hash the password before storing it
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Insert new user into the database
            $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hashed_password);

            if ($stmt->execute()) {
                // Send welcome email after registration
                sendWelcomeEmail($email, $name);

                // Redirect to login page with success message
                echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "Registration Complete!",
                        text: "Registration successful! Please login to continue.",
                        icon: "success",
                        confirmButtonText: "OK"
                    }).then(() => {
                        window.location.href = "index.php";
                    });
                });
              </script>';
                exit();
            } else {
                echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "Registration Failed",
                        text: "There was an issue registering your account. Please try again later.",
                        icon: "error",
                        confirmButtonText: "OK"
                    }).then(() => {
                        window.location.href = "signup.php";
                    });
                });
              </script>';
            }

            $stmt->close();
        }
    }

    $conn->close();
}
