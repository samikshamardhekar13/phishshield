<?php
require_once "config/db.php";

$errors = [];
$success_message = "";

$full_name = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    /*
    |--------------------------------------------------------------------------
    | Step 1: Validate user input
    |--------------------------------------------------------------------------
    */

    if ($full_name === "") {
        $errors[] = "Full name is required.";
    }

    if ($email === "") {
        $errors[] = "Email address is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    if ($password === "") {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must contain at least 8 characters.";
    }

    if ($confirm_password === "") {
        $errors[] = "Please confirm your password.";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Password and confirm password do not match.";
    }

    /*
    |--------------------------------------------------------------------------
    | Step 2: Check whether email already exists
    |--------------------------------------------------------------------------
    */

    if (empty($errors)) {

        $check_email_query = "SELECT id FROM users WHERE email = ? LIMIT 1";
        $check_email_stmt = $conn->prepare($check_email_query);

        if ($check_email_stmt === false) {
            $errors[] = "Something went wrong while checking your email address.";
        } else {
            $check_email_stmt->bind_param("s", $email);
            $check_email_stmt->execute();
            $check_email_stmt->store_result();

            if ($check_email_stmt->num_rows > 0) {
                $errors[] = "An account with this email address already exists.";
            }

            $check_email_stmt->close();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Step 3: Hash password and insert new user
    |--------------------------------------------------------------------------
    */

    if (empty($errors)) {

        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $insert_user_query = "
            INSERT INTO users (full_name, email, password_hash)
            VALUES (?, ?, ?)
        ";

        $insert_user_stmt = $conn->prepare($insert_user_query);

        if ($insert_user_stmt === false) {
            $errors[] = "Something went wrong while creating your account.";
        } else {
            $insert_user_stmt->bind_param(
                "sss",
                $full_name,
                $email,
                $password_hash
            );

            if ($insert_user_stmt->execute()) {
                $success_message = "Account created successfully. You can now log in once the login page is added.";

                $full_name = "";
                $email = "";
            } else {
                $errors[] = "Unable to create your account. Please try again.";
            }

            $insert_user_stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create Account | PhishShield</title>

    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-body">

    <div class="auth-page">

        <div class="auth-brand-section">
            <a href="index.php" class="auth-logo">
                🛡 Phish<span>Shield</span>
            </a>

            <div class="auth-brand-content">
                <p class="auth-badge">ONLINE SAFETY STARTS HERE</p>

                <h1>Stay one step ahead of online scams.</h1>

                <p>
                    Create your PhishShield account to scan suspicious links,
                    save your scan history, and report phishing attempts.
                </p>

                <ul class="auth-benefits">
                    <li>✓ Scan suspicious URLs</li>
                    <li>✓ View your phishing-risk history</li>
                    <li>✓ Report scam websites and messages</li>
                </ul>
            </div>
        </div>

        <div class="auth-form-section">
            <div class="auth-form-box">

                <a href="index.php" class="mobile-auth-logo">
                    🛡 Phish<span>Shield</span>
                </a>

                <p class="form-small-heading">CREATE YOUR ACCOUNT</p>
                <h2>Create Your Account</h2>
                <p class="form-description">
                    Fill in your details to create a free account.
                </p>

                <?php if (!empty($errors)): ?>
                    <div class="message-box error-message">
                        <strong>Please fix the following errors:</strong>
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if ($success_message !== ""): ?>
                    <div class="message-box success-message">
                        <?php echo htmlspecialchars($success_message); ?>
                    </div>
                <?php endif; ?>

                <form action="register.php" method="post">

                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input
                            type="text"
                            id="full_name"
                            name="full_name"
                            placeholder="Enter your full name"
                            value="<?php echo htmlspecialchars($full_name); ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="Enter your email address"
                            value="<?php echo htmlspecialchars($email); ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Create a password"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input
                            type="password"
                            id="confirm_password"
                            name="confirm_password"
                            placeholder="Enter your password again"
                            required
                        >
                    </div>

                    <button type="submit" class="auth-submit-button">
                        Create Account
                    </button>

                </form>

                <p class="auth-switch-text">
                    Already have an account?
                    <a href="login.php">Login here</a>
                </p>

                <a href="index.php" class="back-home-link">
                    ← Back to homepage
                </a>

            </div>
        </div>

    </div>

</body>
</html>