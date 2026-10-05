<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$full_name = $_SESSION["full_name"];
$email = $_SESSION["email"];
$role = $_SESSION["role"];
$is_verified = $_SESSION["is_verified"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard | PhishShield</title>

    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="dashboard-body">

    <nav class="dashboard-navbar">
        <div class="container dashboard-nav-container">

            <a href="index.php" class="logo">
                🛡 Phish<span>Shield</span>
            </a>

            <div class="dashboard-nav-right">
    <span class="dashboard-user-name">
        Hello, <?php echo htmlspecialchars($full_name); ?>
    </span>

    <a href="index.php" class="dashboard-home-link">
        Homepage
    </a>

    <a href="logout.php" class="dashboard-logout-button">
        Logout
    </a>
</div>

        </div>
    </nav>

    <main class="dashboard-main">
        <div class="container">

            <section class="dashboard-welcome">
                <p class="section-label">YOUR ACCOUNT</p>

                <h1>
                    Welcome back, <?php echo htmlspecialchars($full_name); ?>!
                </h1>

                <p>
                    Your PhishShield account is ready. URL scanning, scan history,
                    and scam-report features will appear here in upcoming steps.
                </p>
            </section>

            <section class="dashboard-grid">

                <div class="dashboard-card">
                    <div class="dashboard-card-icon">👤</div>

                    <h2>Account Details</h2>

                    <div class="account-detail">
                        <span>Full Name</span>
                        <strong><?php echo htmlspecialchars($full_name); ?></strong>
                    </div>

                    <div class="account-detail">
                        <span>Email Address</span>
                        <strong><?php echo htmlspecialchars($email); ?></strong>
                    </div>

                    <div class="account-detail">
                        <span>Account Role</span>
                        <strong><?php echo htmlspecialchars(ucfirst($role)); ?></strong>
                    </div>
                </div>

                <div class="dashboard-card">
                    <div class="dashboard-card-icon">🛡</div>

                    <h2>Account Security</h2>

                    <div class="account-detail">
                        <span>Account Status</span>
                        <strong class="status-active">Active</strong>
                    </div>

                    <div class="account-detail">
                        <span>Email Verification</span>

                        <?php if ($is_verified == 1): ?>
                            <strong class="status-active">Verified</strong>
                        <?php else: ?>
                            <strong class="status-pending">Pending</strong>
                        <?php endif; ?>
                    </div>

                    <div class="account-detail">
                        <span>Login Status</span>
                        <strong class="status-active">Logged In</strong>
                    </div>
                </div>

                <div class="dashboard-card dashboard-coming-soon">
                    <div class="dashboard-card-icon">🔎</div>

                    <h2>URL Scanner</h2>

                    <p>
                        Scan suspicious URLs and receive a phishing-risk score.
                    </p>

                    <span class="coming-soon-badge">COMING SOON</span>
                </div>

                <div class="dashboard-card dashboard-coming-soon">
                    <div class="dashboard-card-icon">📜</div>

                    <h2>Scan History</h2>

                    <p>
                        View your previous URL scans and phishing-risk results.
                    </p>

                    <span class="coming-soon-badge">COMING SOON</span>
                </div>

            </section>

        </div>
    </main>

</body>
</html>