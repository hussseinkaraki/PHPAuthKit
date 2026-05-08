<?php
// login.php
session_start();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    include '../config.php';
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $operator = new DataConfig();
    $username = trim($_POST['username'] ?? '');
    $email = $username;
    $pass = $_POST['password'] ?? '';
    if($operator->check($username , $email , $pass)) {
        header("Location: ../index.php");
        exit();
    }
    echo "Invalid password or username.";
} else {
    echo "Invalid request.";
}
?>