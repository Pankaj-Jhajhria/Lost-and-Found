<?php
include "db.php";

$name  = mysqli_real_escape_string($conn, $_POST['name']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$phone = mysqli_real_escape_string($conn, $_POST['phone']);
$pass  = mysqli_real_escape_string($conn, $_POST['pass']);

// Check if email already exists
$check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
if(mysqli_num_rows($check) > 0){
    // Redirect back with error
    header("Location: signup.html?error=exists");
    exit();
}

$result = mysqli_query($conn, "INSERT INTO users(name,email,phone,password)
VALUES('$name','$email','$phone','$pass')");

if($result){
    header("Location: login.html?success=1");
    exit();
} else {
    header("Location: signup.html?error=failed");
    exit();
}
?>
