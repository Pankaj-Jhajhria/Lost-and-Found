<?php
$conn = mysqli_connect(
    "YOUR_DB_HOST",
    "YOUR_DB_USERNAME",
    "YOUR_DB_PASSWORD",
    "YOUR_DB_NAME"
);

if (!$conn) {
    error_log("Database connection failed: " . mysqli_connect_error());
    die("Database connection failed.");
}
?>