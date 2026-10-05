<?php
session_start();

require_once "config/db.php";

if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit;
}

$errors = [];
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    /*
    |--------------------------------------------------------------------------
    | Step 1: Validate login form
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Step 2: Find user and verify password
    |--------------------------------------------------------------------------
    */

    if (empty($errors)) {

        $login_query = "
            SELECT id, full_name, email, password_hash, role, is_verified, account_status
            FROM users
            WHERE email = ?
            LIMIT 1
        ";

        $login_stmt = $conn->prepare($login_query);

        if ($login_stmt === false) {
            $errors[] = "Something went wrong. Please try again later.";
        } else {
            $login_stmt->bind_param("s", $email);
            $login_stmt->execute();

            $result = $login_stmt->get_result();

            if ($result->num_rows === 1) {

                $user = $result->fetch_assoc();

                if (password_verify($password, $user["password_hash"])) {

                    if ($user["account_status"] === "blocked") {
                        $errors[] = "This account is currently blocked. Please contact support.";
                    } else {

                        /*
                        |--------------------------------------------------------------------------
                        | Step 3: Create secure login session
                        |--------------------------------------------------------------------------
                        */

                        session_regenerate_id(true);

                        $_SESSION["user_id"] = $user["id"];
                        $_SESSION["full_name"] = $user["full_name"];
                        $_SESSION["email"] = $user["email"];
                        $_SESSION["role"] = $user["role"];
                        $_SESSION["is_verified"] = $user["is_verified"];

                        header("Location: dashboard.php");
                        exit;
                    }

                } else {
                    $errors[] = "Invalid email or password.";
                }

            } else {
                $errors[] = "Invalid email or password.";
            }

            $login_stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | PhishShield</title>

    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-body">

    <div class="auth-page">

        <div class="auth-brand-section">
            <a href="index.php" class="auth-logo">
                🛡 Phish<span>Shield</span>
            </a>

            <div class="auth-brand-content">
                <p class="auth-badge">WELCOME BACK</p>

                <h1>Your online safety starts with awareness.</h1>

                <p>
                    Log in to access your PhishShield dashboard, review your
                    URL scan history, and help report suspicious websites.
                </p>

                <ul class="auth-benefits">
                    <li>✓ Access your scan history</li>
                    <li>✓ Track suspicious URLs you scanned</li>
                    <li>✓ Help make the web safer for everyone</li>
                </ul>
            </div>
        </div>

        <div class="auth-form-section">
            <div class="auth-form-box">

                <a href="index.php" class="mobile-auth-logo">
                    🛡 Phish<span>Shield</span>
                </a>

                <p class="form-small-heading">ACCOUNT LOGIN</p>

                <h2>Welcome Back</h2>

                <p class="form-description">
                    Enter your account details to continue.
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

                <form action="login.php" method="post">

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
                            placeholder="Enter your password"
                            minlength="8"
                            required
                        >
                    </div>

                    <button type="submit" class="auth-submit-button">
                        Login to Your Account
                    </button>

                </form>

                <p class="auth-switch-text">
                    Do not have an account?
                    <a href="register.php">Register here</a>
                </p>

                <a href="index.php" class="back-home-link">
                    ← Back to homepage
                </a>

            </div>
        </div>

    </div>

</body>
</html>