<?php
session_start();

if(!isset($_SESSION['user'])){
    header("Location: login.html");
    exit();
}

require_once "db.php";
require_once "upload_helper.php";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Reporter's phone number comes from their account (the admin has no account row).
    $email = $_SESSION['user'];
    $stmt  = mysqli_prepare($conn, "SELECT phone FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $user  = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    $phone = $user['phone'] ?? '';

    $item = mb_substr(trim($_POST['item'] ?? ''), 0, 255);
    $desc = trim($_POST['desc'] ?? '');
    $loc  = mb_substr(trim($_POST['loc'] ?? ''), 0, 255);

    list($image, $error) = save_uploaded_image($_FILES['img'] ?? null);
    if($error !== null){
        upload_error_exit($error, "lost.html");
    }

    $type = 'lost';
    $stmt = mysqli_prepare($conn, "INSERT INTO items(type,item_name,description,location,image,phone) VALUES(?,?,?,?,?,?)");
    mysqli_stmt_bind_param($stmt, "ssssss", $type, $item, $desc, $loc, $image, $phone);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: view.php?success=lost");
    exit();
}
?>
