<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.html');
    exit();
}

$sessiontimeout = 1800;
if (time() - $_SESSION['last_activity'] > $sessiontimeout) {
    session_unset();
    session_destroy();
    header('Location: login.html');
    exit();
}
$_SESSION['last_activity'] = time();

include 'config.php';

$operator = new DataConfig();
$alert = null;
if (!isset($_SESSION['csrf_token'])) {
    $operator->refreshCsrfToken();
}

if (isset($_GET['logout'])) {
    $operator->logout();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    if (!$operator->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $type = 'error';
        $message = 'Invalid request token. Please refresh and try again.';
    } else {
        [$type, $message] = $operator->changePassword(
            $_POST['current_password'] ?? '',
            $_POST['new_password'] ?? '',
            $_POST['confirm_password'] ?? ''
        );
        if ($type === 'success') {
            $operator->refreshCsrfToken();
        }
    }
    $alert = ['type' => $type, 'text' => $message];
}

$timeoutMinutes = 30;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MySite - Settings</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/profile.css">
</head>
<body>
    <header>
        <div class="logo">MySite</div>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="settings.php" class="active">Settings</a></li>
                <li>
                    <form action="settings.php" method="get">
                        <button name="logout" type="submit" class="logout-btn">Logout</button>
                    </form>
                </li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h1 class="page-title">Settings</h1>

        <?php if ($alert): ?>
            <div class="alert alert-<?php echo $alert['type'] === 'success' ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($alert['text'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <div class="profile-container">
            <div class="profile-card">
                <h2 class="settings-section-title">Account</h2>
                <p class="settings-hint">Signed in as <?php echo htmlspecialchars($operator->getuserinfo('username'), ENT_QUOTES, 'UTF-8'); ?>.</p>
                <div class="form-group">
                    <label for="settings_username">Username</label>
                    <input type="text" id="settings_username" value="<?php echo htmlspecialchars($operator->getuserinfo('username'), ENT_QUOTES, 'UTF-8'); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="settings_email">Email</label>
                    <input type="email" id="settings_email" value="<?php echo htmlspecialchars($operator->getuserinfo('email'), ENT_QUOTES, 'UTF-8'); ?>" readonly>
                </div>
            </div>

            <div class="profile-card">
                <h2 class="settings-section-title"><i class="fas fa-lock" aria-hidden="true"></i> Security</h2>
                <p class="settings-hint">
                    Sessions expire after <?php echo (int) $timeoutMinutes; ?> minutes of inactivity.
                    Choose a strong password you do not use elsewhere.
                </p>
                <form method="post" action="settings.php">
                    <input type="hidden" name="action" value="change_password">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="form-group">
                        <label for="current_password">Current password</label>
                        <input type="password" id="current_password" name="current_password" required autocomplete="current-password">
                    </div>
                    <div class="form-group">
                        <label for="new_password">New password</label>
                        <input type="password" id="new_password" name="new_password" required minlength="6" autocomplete="new-password">
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm new password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required minlength="6" autocomplete="new-password">
                    </div>
                    <div class="action-buttons">
                        <button type="submit" class="btn btn-primary">Update password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 MySite. All rights reserved.</p>
    </footer>
</body>
</html>
