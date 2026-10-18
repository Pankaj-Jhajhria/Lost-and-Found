<?php
session_start();
if(!isset($_SESSION['admin'])){
    header("Location: index.php");
    exit();
}
include "db.php";

$id = (int) $_GET['id']; // cast to int — prevents SQL injection
mysqli_query($conn, "DELETE FROM items WHERE id=$id");

header("Location: admin.php");
exit();
?>
