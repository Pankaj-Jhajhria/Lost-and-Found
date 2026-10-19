<?php
session_start();
if(!isset($_SESSION['user'])){
    header("Location: login.html");
    exit();
}
include "db.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Search – Lost & Found</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .page-header { padding-top: 52px; }
        .search-wrap { margin-top: 12px; }
        .search-wrap input[type="submit"] {
            width: auto; margin-top: 0; padding: 11px 24px;
        }
    </style>
</head>
<body>

<nav class="navbar">
    <a href="index.php" class="brand">Lost<span>&</span>Found</a>
    <div class="nav-links">
        <a href="lost.html">Report Lost</a>
        <a href="found.html">Report Found</a>
        <a href="view.php">View Items</a>
        <a href="search.php" style="color:var(--text)">Search</a>
        <?php if(isset($_SESSION['admin'])): ?><a href="admin.php">Admin</a><?php endif; ?>
        <a href="logout.php" class="logout">Logout</a>
    </div>
</nav>

<div class="page-header">
    <h1>🔍 Search Items</h1>
    <p>Find lost or found items by name or description</p>
</div>

<form method="GET" class="search-wrap">
    <input type="text" name="search" placeholder="Search by item name…" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
    <input type="submit" value="Search">
</form>

<?php if(isset($_GET['search']) && trim($_GET['search']) !== ''): ?>
<?php
$like   = '%' . addcslashes(trim($_GET['search']), '%_\\') . '%';
$stmt   = mysqli_prepare($conn, "SELECT * FROM items WHERE item_name LIKE ? OR description LIKE ? ORDER BY id DESC");
mysqli_stmt_bind_param($stmt, "ss", $like, $like);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$num    = mysqli_num_rows($result);
?>
<p style="text-align:center; color:var(--muted); font-size:.875rem; margin-bottom:8px;">
    <?php echo $num; ?> result<?php echo $num !== 1 ? 's' : ''; ?> for "<strong style="color:var(--text)"><?php echo htmlspecialchars($_GET['search']); ?></strong>"
</p>

<?php if($num > 0): ?>
<div class="container">
<?php while($row = mysqli_fetch_assoc($result)):
    $msg      = urlencode("Hello! I saw your listing on Lost & Found for: " . $row['item_name']);
    $whatsapp = "https://wa.me/91" . $row['phone'] . "?text=" . $msg;
    $type     = $row['type'];
?>
<div class="card">
    <?php if(!empty($row['image']) && file_exists($row['image'])): ?>
        <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="">
    <?php else: ?>
        <div class="no-image"><?php echo $type === 'lost' ? '🔴' : '🟢'; ?></div>
    <?php endif; ?>
    <div class="card-body">
        <span class="type <?php echo $type; ?>"><?php echo $type === 'lost' ? '● Lost' : '● Found'; ?></span>
        <div class="card-title"><?php echo htmlspecialchars($row['item_name']); ?></div>
        <div class="card-desc"><?php echo htmlspecialchars($row['description']); ?></div>
        <div class="card-meta">
            <span>📍 <?php echo htmlspecialchars($row['location']); ?></span>
        </div>
        <a href="<?php echo $whatsapp; ?>" target="_blank" class="btn whatsapp">💬 Contact on WhatsApp</a>
    </div>
</div>
<?php endwhile; ?>
</div>
<?php else: ?>
<div class="empty">
    <div class="empty-icon">🕵️</div>
    <p>No items matched "<strong><?php echo htmlspecialchars($_GET['search']); ?></strong>". Try a different keyword.</p>
</div>
<?php endif; ?>
<?php endif; ?>

</body>
</html>
