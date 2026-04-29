<?php
//validate form submission
if($_SERVER['REQUEST_METHOD'] != 'POST'){
    die("Invalid request.");
} 
include '../config.php';
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$operator = new DataConfig();
    $username = $_POST['username'];
    $email = $_POST['email'];
    $pass = $_POST['password'];
if($operator->check($username, $email, $pass)){
    die("Username or email already exists.");
}
else{
$operator->signup($username, $email, $pass);
}
?>