<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>XKCD Comic Subscription</title>
</head>
<body>
    <h1>XKCD Comic Subscription</h1>
    
    <!-- Email Registration Form -->
    <form action="index.php" method="post">
        <label for="email">Enter your email:</label><br>
        <input type="email" name="email" id="email" required><br>
        <button type="submit" id="submit-email">Submit</button>
    </form>
    
    <!-- Verification Code Form -->
    <form action="index.php" method="post">
        <label for="verification_code">Enter verification code:</label><br>
        <input type="text" name="verification_code" id="verification_code" maxlength="6" required>
        <input type="hidden" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        <button type="submit" id="submit-verification">Verify</button>
    </form>

    <?php
    require_once 'functions.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['email']) && !isset($_POST['verification_code'])) {
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $code = generateVerificationCode();
                sendVerificationEmail($email, $code);
                session_start();
                $_SESSION['verification_code'] = $code;
                $_SESSION['email'] = $email;
                echo "<p>Verification code sent to $email.</p>";
            } else {
                echo "<p>Invalid email address.</p>";
            }
        } elseif (isset($_POST['verification_code']) && isset($_POST['email'])) {
            session_start();
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $code = $_POST['verification_code'];
            if (verifyCode($email, $code)) {
                registerEmail($email);
                echo "<p>Email $email successfully registered!</p>";
                unset($_SESSION['verification_code']);
                unset($_SESSION['email']);
            } else {
                echo "<p>Invalid verification code.</p>";
            }
        }
    }
    ?>
</body>
</html>