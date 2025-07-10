<?php
session_start();
// Generate a random 5-letter code
$chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
$code = '';
for ($i = 0; $i < 5; $i++) {
    $code .= $chars[rand(0, strlen($chars) - 1)];
}
$_SESSION['captcha_code'] = $code;

// Create image
$img = imagecreatetruecolor(120, 40);
$bg = imagecolorallocate($img, 240, 240, 240);
$fg = imagecolorallocate($img, 60, 60, 60);
$line = imagecolorallocate($img, 180, 180, 180);
imagefilledrectangle($img, 0, 0, 120, 40, $bg);
// Add some noise lines
for ($i = 0; $i < 5; $i++) {
    imageline($img, rand(0,120), rand(0,40), rand(0,120), rand(0,40), $line);
}
// Add the text
$font = __DIR__ . '/assets/fonts/arial.ttf';
if (file_exists($font)) {
    imagettftext($img, 20, rand(-10,10), 18, 30, $fg, $font, $code);
} else {
    imagestring($img, 5, 28, 12, $code, $fg);
}
header('Content-type: image/png');
imagepng($img);
imagedestroy($img);
