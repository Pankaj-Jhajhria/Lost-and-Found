<?php
session_start();

if(!isset($_SESSION['user'])){
    header("Location: login.html");
    exit();
}

include "db.php";

$email = $_SESSION['user'];
$user  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE email='$email'"));
$phone = $user['phone'];

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $item = mysqli_real_escape_string($conn, $_POST['item']);
    $desc = mysqli_real_escape_string($conn, $_POST['desc']);
    $loc  = mysqli_real_escape_string($conn, $_POST['loc']);

    $img_name = basename($_FILES['img']['name']);
    $tmp      = $_FILES['img']['tmp_name'];

    if(!is_dir("images")) mkdir("images", 0755, true);
    move_uploaded_file($tmp, "images/" . $img_name);

    mysqli_query($conn, "INSERT INTO items(type,item_name,description,location,image,phone)
    VALUES('found','$item','$desc','$loc','images/$img_name','$phone')");

    header("Location: view.php?success=found");
    exit();
}
?>
