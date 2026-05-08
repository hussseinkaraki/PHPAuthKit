<?php 
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.html');
    exit();
}

$sessiontimeout = 1800;
if (time() - $_SESSION['last_activity'] > $sessiontimeout) {
    session_destroy();
    header('Location: login.html');
    exit();
}

$_SESSION['last_activity'] = time();

include 'config.php';

$operator = new DataConfig();
if (!isset($_SESSION['csrf_token'])) {
    $operator->refreshCsrfToken();
}

if (isset($_GET['logout'])) {
    $operator->logout();
}

$profileAlert = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    if (!$operator->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $type = 'error';
        $message = 'Invalid request token. Please refresh and try again.';
    } else {
        [$type, $message] = $operator->updateProfile($_POST['username'] ?? '', $_POST['email'] ?? '');
    }
    if ($type === 'success') {
        $operator->refreshCsrfToken();
        header('Location: profile.php?saved=1');
        exit();
    }
    $profileAlert = ['type' => $type, 'text' => $message];
}

if (isset($_GET['saved'])) {
    $profileAlert = ['type' => 'success', 'text' => 'Profile updated successfully.'];
}

$uname = htmlspecialchars($operator->getuserinfo('username'), ENT_QUOTES, 'UTF-8');
$uemail = htmlspecialchars($operator->getuserinfo('email'), ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MySite - Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/profile.css">
</head>
<body>
    <header>
        <div class="logo">MySite</div>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="profile.php" class="active">Profile</a></li>
                <li><a href="settings.php">Settings</a></li>
                <li><form action="profile.php" method="get"><button type="submit" name="logout" class="logout-btn">Logout</button></form></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h1 class="page-title">User Profile</h1>

        <?php if ($profileAlert): ?>
            <div class="alert alert-<?php echo $profileAlert['type'] === 'success' ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($profileAlert['text'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <div class="profile-container">
            <div class="profile-card">
                <div class="profile-header">
                    <div class="avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="profile-info">
                        <h2 id="profileHeaderName"><?php echo $uname; ?></h2>
                        <p id="profileHeaderEmail"><?php echo $uemail; ?></p>
                        <p>Joined: <?php echo htmlspecialchars($operator->getuserinfo('joined'), ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                </div>

                <form id="profileForm" method="post" action="profile.php">
                    <input type="hidden" name="action" value="update_profile">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="profileUsername">Username</label>
                            <input type="text" id="profileUsername" name="username"
                                value="<?php echo $uname; ?>"
                                data-original="<?php echo $uname; ?>"
                                readonly autocomplete="username">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="profileEmail">Email address</label>
                        <input type="email" id="profileEmail" name="email"
                            value="<?php echo $uemail; ?>"
                            data-original="<?php echo $uemail; ?>"
                            readonly autocomplete="email">
                    </div>
                    <div class="action-buttons">
                        <button type="button" class="btn btn-secondary" id="editBtn">Edit profile</button>
                        <button type="button" class="btn btn-secondary" id="cancelBtn" style="display:none;">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveBtn" style="display:none;">Save changes</button>
                    </div>
                </form>
            </div>
        </div>

        <h2 style="margin-bottom: 1.5rem; color: #2d3748;">Profile stats</h2>
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon icon-1">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo (int) $operator->daysactive(); ?></h3>
                    <p>Days active</p>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 MySite. All rights reserved.</p>
    </footer>
    <script>
        (function () {
            var form = document.getElementById('profileForm');
            if (!form) return;
            var usernameInput = document.getElementById('profileUsername');
            var emailInput = document.getElementById('profileEmail');
            var editBtn = document.getElementById('editBtn');
            var cancelBtn = document.getElementById('cancelBtn');
            var saveBtn = document.getElementById('saveBtn');
            var headerName = document.getElementById('profileHeaderName');
            var headerEmail = document.getElementById('profileHeaderEmail');

            function setEditing(on) {
                usernameInput.readOnly = !on;
                emailInput.readOnly = !on;
                editBtn.style.display = on ? 'none' : 'inline-block';
                cancelBtn.style.display = on ? 'inline-block' : 'none';
                saveBtn.style.display = on ? 'inline-block' : 'none';
                if (!on && usernameInput.dataset.original !== undefined) {
                    usernameInput.value = usernameInput.dataset.original;
                    emailInput.value = emailInput.dataset.original;
                }
            }

            editBtn.addEventListener('click', function () {
                setEditing(true);
            });

            cancelBtn.addEventListener('click', function () {
                setEditing(false);
            });

            form.addEventListener('submit', function () {
                usernameInput.dataset.original = usernameInput.value.trim();
                emailInput.dataset.original = emailInput.value.trim();
            });

            if (/\bsaved=1\b/.test(window.location.search)) {
                window.history.replaceState({}, document.title, window.location.pathname);
            }

            if (headerName && headerEmail) {
                headerName.textContent = usernameInput.value;
                headerEmail.textContent = emailInput.value;
            }
        })();
    </script>
</body>
</html>
