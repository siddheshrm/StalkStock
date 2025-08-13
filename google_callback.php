<?php
session_start();

// Load Composer dependencies and environment variables
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Ensure the request contains the authorization code from Google
// The "state" returned by Google must match the one we generated and stored in the session
if (!isset($_GET['code']) || !isset($_GET['state']) || $_GET['state'] !== $_SESSION['oauth2_state']) {
    $_SESSION['alert'] = [
        'type' => 'error',
        'text' => 'Invalid or expired Google login attempt. Please try again.'
    ];
    header('Location: index.php');
    exit();
}
unset($_SESSION['oauth2_state']);

// Authorization code sent by Google, used to request an access token
$code = $_GET['code'];

// Google OAuth 2.0 token endpoint
$token_url = "https://oauth2.googleapis.com/token";

// Required parameters for exchanging the authorization code for an access token
$data = [
    "code" => $code,
    "client_id" => $_ENV['CLIENT_ID'],
    "client_secret" => $_ENV['CLIENT_SECRET'],
    "redirect_uri" => $_ENV['REDIRECT_URI_DEV'],
    "grant_type" => "authorization_code"
];

// Send POST request to get the access token from Google
$options = [
    "http" => [
        "header" => "Content-Type: application/x-www-form-urlencoded\r\n",
        "method" => "POST",
        "content" => http_build_query($data),
    ],
];
$context = stream_context_create($options);
$response = file_get_contents($token_url, false, $context);

if (!$response) {
    $_SESSION['alert'] = [
        'type' => 'error',
        'text' => 'Google authentication failed. Please try again.'
    ];
    header('Location: index.php');
    exit();
}

// Decode the token response and extract the access token
$token_info = json_decode($response, true);
$access_token = $token_info['access_token'] ?? null;

if (!$access_token) {
    $_SESSION['alert'] = [
        'type' => 'error',
        'text' => 'Google authentication failed. Please try again.'
    ];
    header('Location: index.php');
    exit();
}

// Fetch user profile information from Google's API
$user_info_url = "https://www.googleapis.com/oauth2/v2/userinfo?access_token=" . urlencode($access_token);
$user_info = file_get_contents($user_info_url);
if (!$user_info) {
    $_SESSION['alert'] = [
        'type' => 'error',
        'text' => 'Unable to connect to Google to retrieve profile data.'
    ];
    header('Location: index.php');
    exit();
}
$user = json_decode($user_info, true);

// Extract needed data
$email = $user['email'] ?? '';

// Validate email is present
if (empty($email)) {
    $_SESSION['alert'] = ['type' => 'error', 'text' => 'Unable to retrieve email from Google account.'];
    header('Location: index.php');
    exit();
}

// Connect to DB
require 'connection.php';

// Check if user exists
$stmt = $conn->prepare("SELECT id, name, email, is_guest FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $is_guest = $row['is_guest'];

    if ($is_guest) {
        $_SESSION['alert'] = [
            'type' => 'warning',
            'text' => 'You cannot log in as a guest via Google. Please register for a full account.'
        ];
        header('Location: index.php');
        exit();
    }

    // Registered user: log them in
    $_SESSION['email'] = $row['email'];
    $_SESSION['id'] = $row['id'];
    $_SESSION['name'] = $row['name'];

    // Set success message
    $_SESSION['alert'] = [
        'type' => 'success',
        'text' => 'Login successful! Redirecting you to your dashboard.'
    ];

    header('Location: dashboard.php');
    exit();

} else {
    // User not found: deny login and prompt to sign up
    $_SESSION['alert'] = [
        'type' => 'error',
        'text' => 'No account found with this Google account. Please sign up first.'
    ];
    header('Location: index.php');
    exit();
}
