<?php
session_start();

// Generate a random CAPTCHA code
$captcha_code = '';
$characters = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'; // To avoid confusing chars like O, 0, I, l, 1
$captcha_length = 6;

for ($i = 0; $i < $captcha_length; $i++) {
    $captcha_code .= $characters[random_int(0, strlen($characters) - 1)];
}

// Store the CAPTCHA code in the session for verification
$_SESSION['captcha'] = $captcha_code;

// Create an image
$width = 150;
$height = 50;
$image = imagecreatetruecolor($width, $height);

// Set colors
$bg_color = imagecolorallocate($image, 255, 255, 255); // White background
$text_color = imagecolorallocate($image, 0, 0, 0);     // Black text
$line_color = imagecolorallocate($image, 64, 64, 64);  // Gray lines
$dot_color = imagecolorallocate($image, 128, 128, 128); // Light gray dots

// Fill the background
imagefilledrectangle($image, 0, 0, $width, $height, $bg_color);

// Add random lines to make the CAPTCHA harder to read
for ($i = 0; $i < 5; $i++) {
    imageline($image, random_int(0, $width), random_int(0, $height), random_int(0, $width), random_int(0, $height), $line_color);
}

// Add random dots
for ($i = 0; $i < 50; $i++) {
    imagesetpixel($image, random_int(0, $width), random_int(0, $height), $dot_color);
}

// Add the CAPTCHA text
$font_size = 24;
$font_file = __DIR__ . '/fonts/captcha_font.ttf';
if (!file_exists($font_file)) {
    die('Font file not found!');
}
$text_box = imagettfbbox($font_size, 0, $font_file, $captcha_code);
$text_width = $text_box[4] - $text_box[6];
$text_height = $text_box[1] - $text_box[7];
$x = ($width - $text_width) / 2; // Center the text horizontally
$y = ($height + $text_height) / 2; // Center the text vertically

imagettftext($image, $font_size, 0, $x, $y, $text_color, $font_file, $captcha_code);

// Output the image
header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);
