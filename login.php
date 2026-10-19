<?php
session_start();
require_once "db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.html");
    exit();
}

$email = trim($_POST['email'] ?? '');
$pass  = $_POST['pass'] ?? '';

// Administrator: credentials live in db.php (gitignored), not in the users table.
if ($email !== '' && defined('ADMIN_EMAIL') && defined('ADMIN_PASSWORD_HASH')
    && strcasecmp($email, ADMIN_EMAIL) === 0) {
    if (password_verify($pass, ADMIN_PASSWORD_HASH)) {
        session_regenerate_id(true);
        $_SESSION = [];
        $_SESSION['user']  = ADMIN_EMAIL;
        $_SESSION['name']  = 'Admin';
        $_SESSION['phone'] = '';
        $_SESSION['admin'] = true;
        header("Location: index.php");
    } else {
        header("Location: login.html?error=1");
    }
    exit();
}

$stmt = mysqli_prepare($conn, "SELECT name, phone, password FROM users WHERE email = ?");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user   = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if ($user && password_verify($pass, $user['password'])) {
    session_regenerate_id(true);
    $_SESSION = [];
    $_SESSION['user']  = $email;
    $_SESSION['name']  = $user['name'];
    $_SESSION['phone'] = $user['phone'];

    header("Location: index.php");
    exit();
} else {
    header("Location: login.html?error=1");
    exit();
}
?>
