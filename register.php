<?php
include 'connection.php';
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
    $mail->Username = 'email@gmail.com';
    $mail->Password = 'abcd efgh ijkl efgh';    // Note: This is a placeholder password for demonstration purposes.
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    // Sender and recipient
    $mail->setFrom('email@gmail.com', 'StalkStock');
    $mail->addAddress($email);

    // Email subject and body
    $mail->isHTML(true);
    $mail->Subject = 'Welcome to StalkStock!';
    $mail->Body = "<b>Hi $name,</b><br><br>Welcome to StalkStock! We're excited to have you on board.<br>To get started, simply log in and start adding your favorite products to track. You’ll be notified whenever there’s an update!<br><br>Thanks for choosing StalkStock!<br><br><b>Cheers,<br>Team StalkStock</b>";

    // Send the email
    if (!$mail->send()) {
        // Handle email send failure
        showAlert('Error sending email: ' . $mail->ErrorInfo, 'signup.php');
    }
}

// Function to show SweetAlert messages
function showAlert($message, $redirect)
{
    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>';
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: "Error!",
                text: "' . $message . '",
                icon: "error",
                confirmButtonText: "OK"
            }).then(() => {
                window.location.href = "' . $redirect . '";
            });
        });
    </script>';
}

// Registration Logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = strtolower($_POST['email']);
    $password = $_POST['password'];

    // Basic Validation
    if (strlen($name) < 5) {
        showAlert('Name must be at least 5 characters long.', 'signup.php');
        showAlert('Name must be at least 5 characters long.', 'signup.php');
        exit();
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>';
        echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: "Error!",
                text: "Invalid email address.",
                icon: "error",
                confirmButtonText: "OK"
            }).then(() => {
                window.location.href = "signup.php";
            });
        });
      </script>';
        exit();
    } elseif (strlen($password) < 8) {
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>';
        echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "Error!",
                        text: "Password must be at least 8 characters long.",
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
        $stmt = $conn->prepare("SELECT id FROM users WHERE LOWER(email) = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>';
            echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "Error!",
                        text: "Email already exists. Please use a different email.",
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

            // Insert user into the database
            $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hashed_password);

            if ($stmt->execute()) {
                // Send welcome email after registration
                sendWelcomeEmail($email, $name);

                // Send welcome email after registration
                sendWelcomeEmail($email, $name);

                // Redirect to login page with success message
                echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>';
                echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "Success!",
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
                echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>';
                echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "Error!",
                        text: "Unable to register user. Please try again later.",
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
?>
?>