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
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$pass = $_POST['password'] ?? '';
[$status, $message] = $operator->validateInput($username, $email, $pass);
if ($status === 'error') {
    die($message);
}
if($operator->userExists($username, $email)){
    die("Username or email already exists.");
}
[$signupStatus, $signupMessage] = $operator->signup($username, $email, $pass);
if ($signupStatus === 'error') {
    die($signupMessage);
}
?>