<?php
require_once "db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: signup.html");
    exit();
}

$name  = trim($_POST['name']  ?? '');
$email = trim($_POST['email'] ?? '');
$phone = preg_replace('/\D+/', '', $_POST['phone'] ?? '');
$pass  = $_POST['pass'] ?? '';

// Accept "+91 98765 43210" style input; the app prepends 91 for WhatsApp links.
if (strlen($phone) === 12 && substr($phone, 0, 2) === '91') {
    $phone = substr($phone, 2);
}

if ($name === '' || mb_strlen($name) > 100 ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 191 ||
    strlen($phone) !== 10 ||
    $pass === '') {
    header("Location: signup.html?error=failed");
    exit();
}

// The administrator address is reserved and can never be registered here.
if (defined('ADMIN_EMAIL') && strcasecmp($email, ADMIN_EMAIL) === 0) {
    header("Location: signup.html?error=exists");
    exit();
}

// Check if email already exists
$stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
$exists = mysqli_stmt_num_rows($stmt) > 0;
mysqli_stmt_close($stmt);

if ($exists) {
    header("Location: signup.html?error=exists");
    exit();
}

$hash = password_hash($pass, PASSWORD_DEFAULT);

$stmt = mysqli_prepare($conn, "INSERT INTO users(name,email,phone,password) VALUES(?,?,?,?)");
mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $phone, $hash);

$ok   = false;
$code = 0;
try {
    $ok = mysqli_stmt_execute($stmt);
} catch (mysqli_sql_exception $e) {
    $code = $e->getCode();
}
if (!$ok && $code === 0) {
    $code = mysqli_stmt_errno($stmt);
}
mysqli_stmt_close($stmt);

if ($ok) {
    header("Location: login.html?success=1");
} elseif ($code === 1062) {
    // Duplicate email (two signups raced past the check above)
    header("Location: signup.html?error=exists");
} else {
    header("Location: signup.html?error=failed");
}
exit();
?>
