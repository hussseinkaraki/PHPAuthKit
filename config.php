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
            session_regenerate_id(true);
            $_SESSION['username'] = $row['name'];
            $_SESSION['last_activity'] = time();
            $_SESSION['email'] = $row['email'];
            $_SESSION['joined'] = $row['joined'];
            return true;
       }}
      return false;
    }
    function userExists($username, $email) {
      global $conn;
      $username = trim($username);
      $email = trim($email);
      $stmt = $conn->prepare("SELECT 1 FROM test1 WHERE name = ? OR email = ? LIMIT 1");
      if (!$stmt) {
        return false;
      }
      $stmt->bind_param("ss", $username, $email);
      $stmt->execute();
      $results = $stmt->get_result();
      return $results->num_rows > 0;
    }
    function validateInput($username, $email, $pass) {
      $username = trim($username);
      $email = trim($email);
      if ($username === '' || $pass === '' || $email === '') {
        return ['error', 'Please fill all required fields.'];
      }
      if (!preg_match("/^[a-zA-Z0-9_]+$/", $username)) {
        return ['error', 'Username can only contain letters, numbers, and underscores.'];
      }
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['error', 'Invalid email format.'];
      }
      if (strlen($pass) < 6) {
        return ['error', 'Password must be at least 6 characters long.'];
      }
      return ['success', 'OK'];
    }
    function verifyCsrfToken($token) {
      if (!isset($_SESSION['csrf_token']) || !is_string($token)) {
        return false;
      }
      return hash_equals($_SESSION['csrf_token'], $token);
    }
    function refreshCsrfToken() {
      $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
      return $_SESSION['csrf_token'];
    }
    function signup($username, $email, $pass) {
      [$status, $message] = $this->validateInput($username, $email, $pass);
      if ($status === 'error') {
        return ['error', $message];
      }
      $joined = date("Y-m-d H:i:s");
      global $conn;
      $stmt = $conn->prepare("INSERT INTO test1 (name, email, pass, joined) VALUES (?, ?, ?, ?)");
      $hashedPass = password_hash($pass, PASSWORD_DEFAULT);
      $stmt->bind_param("ssss", $username, $email, $hashedPass, $joined);
      $stmt->execute();
      if ($stmt->affected_rows > 0) {
      session_start();
      session_regenerate_id(true);
      $_SESSION['username'] = $username;
      $_SESSION['email'] = $email;
      $_SESSION['last_activity'] = time();
      $_SESSION['joined'] = $joined;
      header("Location: ../index.php");
      exit();
      return ['success', 'Account created successfully.'];} else {
      return ['error', 'Could not create account. Please try again.'];
      $conn->close();}}
    function logout(){
    session_unset();
    session_destroy();
    header("Location: login.html");
    exit();
}
    function changePassword($currentPass, $newPass, $confirmPass) {
        if ($newPass !== $confirmPass) {
            return ['error', 'New passwords do not match.'];
        }
        if (strlen($newPass) < 6) {
            return ['error', 'Password must be at least 6 characters long.'];
        }
        global $conn;
        $name = $_SESSION['username'];
        $stmt = $conn->prepare("SELECT pass FROM test1 WHERE name = ? LIMIT 1");
        if (!$stmt) {
            return ['error', 'Something went wrong. Please try again.'];
        }
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows === 0) {
            return ['error', 'Account not found.'];
        }
        $row = $res->fetch_assoc();
        if (!password_verify($currentPass, $row['pass'])) {
            return ['error', 'Current password is incorrect.'];
        }
        $newHash = password_hash($newPass, PASSWORD_DEFAULT);
        $upd = $conn->prepare("UPDATE test1 SET pass = ? WHERE name = ?");
        if (!$upd) {
            return ['error', 'Something went wrong. Please try again.'];
        }
        $upd->bind_param("ss", $newHash, $name);
        if (!$upd->execute()) {
            return ['error', 'Could not update password.'];
        }
        return ['success', 'Your password was updated successfully.'];
    }
    function updateProfile($username, $email) {
        $username = trim($username);
        $email = trim($email);
        if ($username === '' || $email === '') {
            return ['error', 'Please fill all fields.'];
        }
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            return ['error', 'Username can only contain letters, numbers, and underscores.'];
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['error', 'Invalid email format.'];
        }
        global $conn;
        $currentName = $_SESSION['username'];
        if ($username !== $currentName) {
            $stmt = $conn->prepare('SELECT name FROM test1 WHERE name = ? LIMIT 1');
            if (!$stmt) {
                return ['error', 'Something went wrong. Please try again.'];
            }
            $stmt->bind_param('s', $username);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                return ['error', 'That username is already taken.'];
            }
        }
        $stmt = $conn->prepare('SELECT name FROM test1 WHERE email = ? AND name <> ? LIMIT 1');
        if (!$stmt) {
            return ['error', 'Something went wrong. Please try again.'];
        }
        $stmt->bind_param('ss', $email, $currentName);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            return ['error', 'That email is already in use.'];
        }
        $upd = $conn->prepare('UPDATE test1 SET name = ?, email = ? WHERE name = ?');
        if (!$upd) {
            return ['error', 'Something went wrong. Please try again.'];
        }
        $upd->bind_param('sss', $username, $email, $currentName);
        if (!$upd->execute()) {
            return ['error', 'Could not save your profile.'];
        }
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        return ['success', 'Profile updated successfully.'];
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
    elseif ($field == "joined") {
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