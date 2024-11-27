<?php
session_start();

include 'connection.php';
echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = strtolower($_POST['email']);
    $password = $_POST['password'];

    // Query the database to check if the user exists
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stored_password = $row['password'];
        $is_guest = $row['is_guest'];

        if ($is_guest) {
            // If the user is a guest, show a specific alert for guests
            echo '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            title: "Guest Login",
                            text: "You cannot log in as a guest with a password. Please register for a full account.",
                            icon: "warning",
                            confirmButtonText: "OK"
                        }).then(() => {
                            window.location.href = "index.php";
                        });
                    });
                  </script>';
            exit();
        } else {
            // Verify the hashed password for non-guest users
            if (password_verify($password, $stored_password)) {
                $_SESSION['email'] = $email;
                $_SESSION['id'] = $row['id'];
                $_SESSION['name'] = $row['name'];

                // Redirect to dashboard.php after successful login
                echo '<script>
                        document.addEventListener("DOMContentLoaded", function() {
                            Swal.fire({
                                title: "Welcome Back!",
                                text: "Login successful! Redirecting you to your dashboard.",
                                icon: "success",
                                confirmButtonText: "OK"
                            }).then(() => {
                                window.location.href = "dashboard.php";
                            });
                        });
                      </script>';
                exit();
            } else {
                // Incorrect password for registered users
                echo '<script>
                        document.addEventListener("DOMContentLoaded", function() {
                            Swal.fire({
                                title: "Incorrect Password",
                                text: "The password you entered is incorrect. Please try again.",
                                icon: "error",
                                confirmButtonText: "OK"
                            }).then(() => {
                                window.location.href = "index.php";
                            });
                        });
                      </script>';
                exit();
            }
        }
    } else {
        // User does not exist
        echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "User Not Found",
                        text: "No user was found with that email address. Please check and try again.",
                        icon: "error",
                        confirmButtonText: "OK"
                    }).then(() => {
                        window.location.href = "index.php";
                    });
                });
              </script>';
        exit();
    }
}

$conn->close();
