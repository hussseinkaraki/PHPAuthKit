<?php 
session_start();
if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
};
$sessiontimeout = 1800;
if(time() - $_SESSION['last_activity'] > $sessiontimeout){
    session_unset();
    session_destroy();
    header("Location: login.php");
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
<html>
<head>
    <title>Main Page</title>
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Main Page</title>
</head>
<body>
    <!-- Header with Navigation and Logout -->
    <header>
        <div class="header-container">
            <div class="logo">My<span>Site</span></div>
            
            <nav>
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="#">Settings</a></li>
                    <li>
                        <form action="index.php" method="get">
                        <button name="logout" class="logout" id="logoutBtn" type="submit">
                            Logout
                        </button>
                        </form>
                    </li>
                </ul>
            </nav>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="main-content">
        <section class="welcome-section">
            <h1>Welcome to Your Dashboard</h1>
            <p>
                This is a simple main page with a clean design. The logout button in the header allows you to securely sign out of your account. You can add more content and functionality as needed.
            </p>
        </section>
        
        <div class="content-boxes">
            <div class="box">
                <h2>Recent Activity</h2>
                <p>Your recent account activity will appear here. This could include login history, file uploads, or other actions.</p>
            </div>
            
            <div class="box">
                <h2>Notifications</h2>
                <p>You have no new notifications. When you receive alerts or messages, they will show up here.</p>
            </div>
            
            <div class="box">
                <h2>Quick Links</h2>
                <p>Links to important sections of the application will appear here for quick access.</p>
            </div>
        </div>
    </main>
    
    <!-- Footer -->
    <footer>
        <p>&copy; 2026 MySite. All rights reserved.</p>
    </footer>

</body>
</html> 
