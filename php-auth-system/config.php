<?php
# Load environment variables (.env)
function loadEnvFile($path) {
    if (!file_exists($path)) {
        die("Error: .env file not found at $path");
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue; 
        }
        
        // Parse KEY=VALUE
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $key = trim($parts[0]);
            $value = trim($parts[1]);
            
            // Set in all possible locations
            putenv("$key=$value");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}
if(file_exists("../.env")){
    loadEnvFile("../.env");
}
else{
    loadEnvFile(".env");
}

$host = getenv('DB_HOST');
$username = getenv('DB_USER');
$password = getenv('DB_PASSWORD');
$dbname = getenv('DB_NAME');
$conn = new mysqli($host, $username, $password, $dbname);
class Dataconfig {
    public $username;
    public $email;
    public $pass;
    #method to check credetials for login or signup
    function check($username , $email , $pass){
      global $conn;
      $username = trim($username);
      $email = trim($email);
      $stmt = $conn->prepare("SELECT * FROM test1 WHERE name = ? OR email = ?");
      $stmt->bind_param("ss", $username, $email);
      $stmt->execute();
      $results = $stmt->get_result(); 
      if($results->num_rows > 0){
         $row = $results->fetch_assoc();
         if(password_verify($pass, $row['pass'])){
            $_SESSION['username'] = $row['name'];
            $_SESSION['last_activity'] = time();
            $_SESSION['email'] = $row['email'];
            $_SESSION['joined'] = $row['joined'];
            return true;
       }}else {
        return false;}}
    function signup($username, $email, $pass) {
      function validateInput($username, $email, $pass) {
        #check if any field is empty
        if(empty($_POST["username"]) || empty($_POST["password"]) || empty($_POST["email"])){
           die("Please fill all required fields.");}
            #username validation
        if(!preg_match("/^[a-zA-Z0-9_]+$/", $_POST["username"])){
           die("Username can only contain letters, numbers, and underscores.");}
           #email 
        if(!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){
           die("Invalid email format.");} 
           #password strength
        if(strlen($_POST["password"]) < 6){
           die("Password must be at least 6 characters long.");}}
    
      validateInput($username, $email, $pass);
      $joined = date("Y-m-d H:i:s");
      global $conn;
      $stmt = $conn->prepare("INSERT INTO test1 (name, email, pass, joined) VALUES (?, ?, ?, ?)");
      $hashedPass = password_hash($pass, PASSWORD_DEFAULT);
      $stmt->bind_param("ssss", $username, $email, $hashedPass, $joined);
      $stmt->execute();
      if ($stmt->affected_rows > 0) {
      session_start();
      $_SESSION['username'] = $username;
      $_SESSION['email'] = $email;
      $_SESSION['last_activity'] = time();
      $_SESSION['joined'] = $joined;
      header("Location: ../index.php");
      exit();
      return true;} else {
      echo "Error: " . $stmt->error . "<br>" . $conn->error;
      return false;
      $conn->close();}}
    function logout(){
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
//get user info from session
function getuserinfo($field){
    if($field == 'username'){
        return $_SESSION['username'];
    }
    elseif($field == "email")
    {
        return $_SESSION['email'];
    }
    elseif ($field = "joined") {
        return $_SESSION['joined'];
    }
    return 'error';
}
//get days active
function daysactive(){
    $joined = new DateTime($_SESSION['joined']);
    $now = new DateTime();
    $interval = $joined->diff($now);
    return $interval->days;
}
};