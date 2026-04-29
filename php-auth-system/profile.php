<?php 
session_start();
if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
};
$sessiontimeout = 1800;
if(time() - $_SESSION['last_activity'] > $sessiontimeout){
    session_destroy();
    header("Location: login.php");
    exit();
}
else{
    $_SESSION['last_activity'] = time();
};
include 'config.php';
if (isset($_GET['logout'])) {
    logout();
}
$operator = new DataConfig();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MySite - Profile</title>
    <link rel="stylesheet" href="css/profile.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="logo">MySite</div>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="#" class="active">Profile</a></li>
                <li><a href="settings.html">Settings</a></li>
                <li><form action = "profile.php" method = "get"><button name = "logout"  class="logout-btn">Logout</button></form></li>
            </ul>
        </nav>
    </header>

    <!-- Main Content -->
    <div class="container">
        <h1 class="page-title">User Profile</h1>
        
        <!-- Profile Card -->
        <div class="profile-container">
            <div class="profile-card">
                <div class="profile-header">
                    <div class="avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="profile-info">
                        <h2 name = "username"><?php 
                        echo $operator->getuserinfo('username');
                        ?></h2>
                        <p name = "email"><?php 
                        echo $operator->getuserinfo('email');
                        ?>
                    </p>
                        <p name = "joined">Joined: <?php echo $operator->getuserinfo('joined') ?></p>
                    </div>
                </div>
                
                <form>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstName">username</label>
                            <input type="text" id="firstName" value="<?php 
                             echo $operator->getuserinfo('username');
                            ?>" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" value="<?php 
                        echo $operator->getuserinfo('email');
                        ?>" readonly>
                    </div>
                    <div class="action-buttons">
                        <button type="button" class="btn btn-secondary" id="editBtn">Edit Profile</button>
                        <button type="submit" class="btn btn-primary" id="saveBtn" style="display:none;">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Stats Section -->
        <h2 style="margin-bottom: 1.5rem; color: #2d3748;">Profile Stats</h2>
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon icon-1">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <h3><?php 
                        echo $operator->daysactive();
                                        ?></h3>
                    <p>Days Active</p>
                </div>
            </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2023 MySite. All rights reserved.</p>
    </footer>
    <style>
        /* Toggle edit mode with CSS only */
        #editBtn:focus ~ form input:read-only,
        #editBtn:focus ~ form textarea:read-only,
        #editBtn:focus ~ form select:disabled {
            background-color: #f9f9f9;
            border-color: #cbd5e0;
            cursor: text;
        }
        
        #editBtn:focus {
            display: none;
        }
        
        #editBtn:focus + #saveBtn {
            display: inline-block;
        }
        
        /* Simulate form editing with CSS focus states */
        #editBtn:focus ~ form input[type="text"],
        #editBtn:focus ~ form input[type="email"],
        #editBtn:focus ~ form input[type="tel"],
        #editBtn:focus ~ form textarea,
        #editBtn:focus ~ form select {
            background-color: #fff;
            border-color: #a0aec0;
        }
    </style>
</body>
</html>