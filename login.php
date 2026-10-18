<?php
session_start();
include "db.php";

$email = mysqli_real_escape_string($conn, $_POST['email']);
$pass  = mysqli_real_escape_string($conn, $_POST['pass']);

$result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND password='$pass'");

if(mysqli_num_rows($result) > 0){
    $user = mysqli_fetch_assoc($result);
    $_SESSION['user']  = $email;
    $_SESSION['name']  = $user['name'];
    $_SESSION['phone'] = $user['phone'];

    if($email == "admin@gmail.com"){
        $_SESSION['admin'] = true;
    }

    header("Location: index.php");
    exit();
} else {
    header("Location: login.html?error=1");
    exit();
}
?>
