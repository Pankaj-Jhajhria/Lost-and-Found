<?php
session_start();
if(!isset($_SESSION['admin'])){
    header("Location: index.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(405);
    header("Allow: POST");
    exit("Method not allowed.");
}

$token = $_POST['csrf_token'] ?? '';
if(empty($_SESSION['csrf_token']) || !is_string($token) || !hash_equals($_SESSION['csrf_token'], $token)){
    http_response_code(403);
    exit("Invalid or expired request. Go back to the admin panel and try again.");
}

require_once "db.php";

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
if($id !== false && $id !== null && $id > 0){
    $stmt = mysqli_prepare($conn, "DELETE FROM items WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

header("Location: admin.php");
exit();
?>
