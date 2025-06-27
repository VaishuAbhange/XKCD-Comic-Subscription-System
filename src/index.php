<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>XKCD Comic Subscription</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 2em;
        }
        form {
            margin-bottom: 1.5em;
            padding: 1em;
            border: 1px solid #ccc;
            width: 300px;
        }
        input, button {
            margin-top: 0.5em;
            width: 100%;
            padding: 0.4em;
        }
    </style>
</head>
<body>
    <h1>XKCD Comic Subscription</h1>

    <?php
    session_start();
    require_once 'functions.php';

    // CSRF protection
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    // Handle form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (
            isset($_POST['csrf_token']) &&
            hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
        ) {
            if (isset($_POST['email']) && !isset($_POST['verification_code'])) {
                $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $code = generateVerificationCode();
                    sendVerificationEmail($email, $code);
                    $_SESSION['verification_code'] = $code;
                    $_SESSION['email'] = $email;
                    echo "<p>Verification code sent to <strong>$email</strong>.</p>";
                } else {
                    echo "<p style='color:red;'>Invalid email address.</p>";
                }
            } elseif (isset($_POST['verification_code']) && isset($_POST['email'])) {
                $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
                $code = $_POST['verification_code'];
                if (
                    isset($_SESSION['verification_code']) &&
                    $code === $_SESSION['verification_code'] &&
                    $email === $_SESSION['email']
                ) {
                    registerEmail($email);
                    echo "<p>Email <strong>$email</strong> successfully registered!</p>";
                    unset($_SESSION['verification_code'], $_SESSION['email']);
                } else {
                    echo "<p style='color:red;'>Invalid verification code.</p>";
                }
            }
        } else {
            echo "<p style='color:red;'>Invalid session or CSRF token.</p>";
        }
    }
    ?>

    <!-- Email Registration Form -->
    <form action="index.php" method="post">
        <label for="email">Enter your email:</label><br>
        <input type="email" name="email" id="email" required><br>
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <button type="submit" id="submit-email">Submit</button>
    </form>

    <!-- Verification Code Form -->
    <form action="index.php" method="post">
        <label for="verification_code">Enter verification code:</label><br>
        <input type="text" name="verification_code" id="verification_code" maxlength="6" required>
        <input type="hidden" name="email" value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <button type="submit" id="submit-verification">Verify</button>
    </form>

</body>
</html>
