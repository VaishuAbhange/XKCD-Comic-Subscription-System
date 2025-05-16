<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Unsubscribe from XKCD Comics</title>
</head>
<body>
    <h1>Unsubscribe from XKCD Comics</h1>
    
    <!-- Unsubscribe Email Form -->
    <form action="unsubscribe.php" method="post">
        <label for="unsubscribe_email">Enter your email:</label><br>
        <input type="email" name="unsubscribe_email" id="unsubscribe_email" required><br>
        <button type="submit" id="submit-unsubscribe">Unsubscribe</button>
    </form>
    
    <!-- Unsubscribe Verification Code Form -->
    <form action="unsubscribe.php" method="post">
        <label for="verification_code">Enter verification code:</label><br>
        <input type="text" name="verification_code" id="verification_code" maxlength="6" required>
        <input type="hidden" name="unsubscribe_email" value="<?php echo isset($_POST['unsubscribe_email']) ? htmlspecialchars($_POST['unsubscribe_email']) : ''; ?>">
        <button type="submit" id="submit-verification">Verify</button>
    </form>

    <?php
    require_once 'functions.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['unsubscribe_email']) && !isset($_POST['verification_code'])) {
            $email = filter_var($_POST['unsubscribe_email'], FILTER_SANITIZE_EMAIL);
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $code = generateVerificationCode();
                $headers = "From: no-reply@example.com\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                $subject = "Confirm Un-subscription";
                $body = "<p>To confirm un-subscription, use this code: <strong>$code</strong></p>";
                mail($email, $subject, $body, $headers);
                session_start();
                $_SESSION['unsubscribe_code'] = $code;
                $_SESSION['unsubscribe_email'] = $email;
                echo "<p>Unsubscribe verification code sent to $email.</p>";
            } else {
                echo "<p>Invalid email address.</p>";
            }
        } elseif (isset($_POST['verification_code']) && isset($_POST['unsubscribe_email'])) {
            session_start();
            $email = filter_var($_POST['unsubscribe_email'], FILTER_SANITIZE_EMAIL);
            $code = $_POST['verification_code'];
            if (isset($_SESSION['unsubscribe_code']) && $_SESSION['unsubscribe_code'] === $code && $_SESSION['unsubscribe_email'] === $email) {
                unsubscribeEmail($email);
                echo "<p>Email $email successfully unsubscribed!</p>";
                unset($_SESSION['unsubscribe_code']);
                unset($_SESSION['unsubscribe_email']);
            } else {
                echo "<p>Invalid verification code.</p>";
            }
        }
    }
    ?>
</body>
</html>