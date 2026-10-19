<?php
// Copy this file to db.php and fill in your real values.
// db.php is listed in .gitignore and must never be committed.
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

mysqli_set_charset($conn, "utf8mb4");

// Administrator login. This account is NOT stored in the database and cannot
// be created through signup.php (that email address is reserved).
// Generate the hash on any machine with PHP:
//   php -r "echo password_hash('YourStrongPassword', PASSWORD_DEFAULT);"
define('ADMIN_EMAIL', 'admin@example.com');
define('ADMIN_PASSWORD_HASH', 'PASTE_PASSWORD_HASH_HERE');
?>
