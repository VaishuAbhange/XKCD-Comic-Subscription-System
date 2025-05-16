<?php

function generateVerificationCode() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

function registerEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        file_put_contents($file, $email . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}

function unsubscribeEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    if (file_exists($file)) {
        $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $emails = array_filter($emails, function($line) use ($email) {
            return trim($line) !== $email;
        });
        file_put_contents($file, implode(PHP_EOL, $emails) . PHP_EOL, LOCK_EX);
    }
}

function sendVerificationEmail($email, $code) {
    $headers = "From: no-reply@example.com\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $subject = "Your Verification Code";
    $body = "<p>Your verification code is: <strong>$code</strong></p>";
    mail($email, $subject, $body, $headers);
}

function verifyCode($email, $code) {
    session_start();
    return isset($_SESSION['verification_code']) && $_SESSION['verification_code'] === $code && $_SESSION['email'] === $email;
}

function fetchAndFormatXKCDData() {
    $comicId = rand(1, 3000); // Safe range for XKCD comics as of May 2025
    $url = "https://xkcd.com/$comicId/info.0.json";
    $response = @file_get_contents($url);
    
    if ($response === false) {
        return "<p>Error fetching XKCD comic.</p>";
    }
    
    $data = json_decode($response, true);
    if (!$data) {
        return "<p>Error decoding XKCD data.</p>";
    }
    
    $imageUrl = $data['img'];
    $title = htmlspecialchars($data['title']);
    
    return "<h2>XKCD Comic: $title</h2><img src='$imageUrl' alt='XKCD Comic'>";
}

function sendXKCDUpdatesToSubscribers() {
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) {
        return;
    }
    
    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (empty($emails)) {
        return;
    }
    
    $comicHtml = fetchAndFormatXKCDData();
    $unsubscribeUrl = "http://" . $_SERVER['HTTP_HOST'] . "/unsubscribe.php";
    $body = $comicHtml . "<p><a href='$unsubscribeUrl' id='unsubscribe-button'>Unsubscribe</a></p>";
    
    $headers = "From: no-reply@example.com\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $subject = "Your XKCD Comic";
    
    foreach ($emails as $email) {
        $email = trim($email);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            mail($email, $subject, $body, $headers);
        }
    }
}