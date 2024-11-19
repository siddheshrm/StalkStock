<?php
$servername = "localhost";
$username = "root";
$password = "password";
$dbname = "dbname";
$port = "port";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    echo '<script>alert("Connection failed");</script>';
    echo '<script>window.location = "index.php";</script>';
    exit;
} else {
    // echo "Connected successfully";
}
