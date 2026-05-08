<?php 
session_start();
if(!isset($_SESSION['username'])){
    header("Location: login.html");
    exit();
};
$sessiontimeout = 1800;
if(time() - $_SESSION['last_activity'] > $sessiontimeout){
    session_unset();
    session_destroy();
    header("Location: login.html");
    exit();
}
else{
    $_SESSION['last_activity'] = time();
};
function logout(){
    session_destroy();
    header("Location: login.html");
    exit();
}
if (isset($_GET['logout'])) {
    logout();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MySite - Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/profile.css">
</head>
<body>
    <header>
        <div class="logo">MySite</div>
        <nav>
            <ul>
                <li><a href="index.php" class="active">Home</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="settings.php">Settings</a></li>
                <li>
                    <form action="index.php" method="get">
                        <button name="logout" type="submit" class="logout-btn">Logout</button>
                    </form>
                </li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h1 class="page-title">Welcome to Your Dashboard</h1>

        <div class="profile-container">
            <div class="profile-card" style="flex: 1 1 100%; max-width: 100%;">
                <p style="color: #718096; font-size: 1.05rem;">
                    You're signed in. Use the header to open your profile, settings, or sign out securely.
                    Add more widgets and shortcuts below as your app grows.
                </p>
            </div>
        </div>

        <h2 style="margin-bottom: 1.5rem; color: #2d3748;">Overview</h2>
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon icon-1">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3>Recent Activity</h3>
                    <p>Login history, uploads, and other actions will show here.</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-2">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="stat-info">
                    <h3>Notifications</h3>
                    <p>No new alerts yet. Messages and alerts appear here.</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-3">
                    <i class="fas fa-link"></i>
                </div>
                <div class="stat-info">
                    <h3>Quick Links</h3>
                    <p>Shortcuts to important areas of your app go here.</p>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 MySite. All rights reserved.</p>
    </footer>
</body>
</html>
