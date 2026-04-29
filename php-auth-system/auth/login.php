<?php
// login.php
          session_start();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    include '../config.php';
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $operator = new DataConfig();
    $username = $_POST['username'];
    $email = $_POST['username'];
    $pass = $_POST['password'];
        if($operator->check($username , $email , $pass)) {
            header("Location: ../index.php");
            exit();
        } else {
            echo "Invalid password or username.";
        }
    } else {
        echo "No user found with that username or email.";
    };
?>