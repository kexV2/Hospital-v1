<?php
session_start();
$con = mysqli_connect("localhost","root","","myhmsdb");

// -----------------------------------------
// RATE LIMITING FOR ADMIN LOGIN
// 3 attempts, 60-second lockout
// -----------------------------------------
if (!isset($_SESSION['adminLoginAttempts'])) {
    $_SESSION['adminLoginAttempts'] = 0;
    $_SESSION['adminTimeOut']       = 0;
}

$attempt_limit   = 3;
$lockout_seconds = 60;

// Check lockout before processing login
if ($_SESSION['adminLoginAttempts'] >= $attempt_limit) {
    $remaining = $_SESSION['adminTimeOut'] - time();
    if ($remaining > 0) {
        die("Too many failed admin login attempts. Try again in $remaining seconds.");
    } else {
        // Reset after timeout expires
        $_SESSION['adminLoginAttempts'] = 0;
        $_SESSION['adminTimeOut']       = 0;
    }
}

// -----------------------------------------
// ADMIN LOGIN (existing base logic)
// -----------------------------------------
if (isset($_POST['adsub'])) {
    $username = $_POST['username1'];
    $password = $_POST['password2'];

    // Get admin record by username only (existing logic)
    $query  = "SELECT * FROM admintb WHERE username='$username' LIMIT 1;";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

        // Allow both hashed and legacy plaintext passwords (existing logic)
        if (password_verify($password, $row['password']) || $password === $row['password']) {

            // Reset attempts on success
            $_SESSION['adminLoginAttempts'] = 0;
            $_SESSION['adminTimeOut']       = 0;

            $_SESSION['username'] = $username;
            header("Location:admin-panel1.php");
            exit();
        }
    }

    // FAILED LOGIN PATH (base logic + new counters)
    $_SESSION['adminLoginAttempts']++;

    if ($_SESSION['adminLoginAttempts'] >= $attempt_limit) {
        $_SESSION['adminTimeOut'] = time() + $lockout_seconds;
        die("Too many failed admin login attempts. You are locked out for $lockout_seconds seconds.");
    }

    echo "<script>alert('Invalid Username or Password. Try Again!'); 
          window.location.href = 'index.php';</script>";
    exit();
}
?>
