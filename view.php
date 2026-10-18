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
    <title>View Items – Lost & Found</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .page-header { padding-top: 52px; }
        .filter-bar {
            display: flex; justify-content: center; gap: 10px;
            padding: 0 24px 32px; flex-wrap: wrap;
        }
        .filter-btn {
            padding: 8px 20px; border-radius: 20px; font-size: 0.82rem;
            font-weight: 600; letter-spacing: 0.04em; cursor: pointer;
            border: 1px solid var(--border); background: var(--surface);
            color: var(--muted); transition: all .2s; text-transform: uppercase;
        }
        .filter-btn:hover, .filter-btn.active { color: var(--text); border-color: var(--accent); background: rgba(108,99,255,0.1); }
        .filter-btn.f-lost.active  { border-color: var(--lost);  background: rgba(255,101,132,0.1); color: var(--lost); }
        .filter-btn.f-found.active { border-color: var(--found); background: rgba(34,211,165,0.1);  color: var(--found); }

        .success-banner {
            max-width: 500px; margin: 0 auto 8px; padding: 13px 18px;
            border-radius: 10px; font-size: .9rem; font-weight: 500; text-align: center;
            background: rgba(34,211,165,0.12); color: var(--found);
            border: 1px solid rgba(34,211,165,0.25);
        }
    </style>
</head>
<body>

<nav class="navbar">
    <a href="index.php" class="brand">Lost<span>&</span>Found</a>
    <div class="nav-links">
        <a href="lost.html">Report Lost</a>
        <a href="found.html">Report Found</a>
        <a href="view.php" style="color:var(--text)">View Items</a>
        <a href="search.php">Search</a>
        <?php if(isset($_SESSION['admin'])): ?><a href="admin.php">Admin</a><?php endif; ?>
        <a href="logout.php" class="logout">Logout</a>
    </div>
</nav>

<?php if(isset($_GET['success'])): ?>
<div class="success-banner">
    ✅ <?php echo $_GET['success'] === 'lost' ? 'Lost item reported successfully!' : 'Found item reported successfully!'; ?>
    Others can now see it.
</div>
<?php endif; ?>

<div class="page-header">
    <h1>All Items</h1>
    <p>Browse every lost and found listing</p>
</div>

<div class="filter-bar">
    <button class="filter-btn active" onclick="filterCards('all', this)">All</button>
    <button class="filter-btn f-lost"  onclick="filterCards('lost', this)">🔴 Lost</button>
    <button class="filter-btn f-found" onclick="filterCards('found', this)">🟢 Found</button>
</div>

<div class="container" id="card-grid">
<?php
$result = mysqli_query($conn, "SELECT * FROM items ORDER BY id DESC");
while($row = mysqli_fetch_assoc($result)):
    $msg      = urlencode("Hello! I saw your listing on Lost & Found for: " . $row['item_name']);
    $whatsapp = "https://wa.me/91" . $row['phone'] . "?text=" . $msg;
    $type     = $row['type'];
?>
<div class="card" data-type="<?php echo $type; ?>">
    <?php if(!empty($row['image']) && file_exists($row['image'])): ?>
        <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['item_name']); ?>">
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
        <a href="<?php echo $whatsapp; ?>" target="_blank" class="btn whatsapp">
            💬 Contact on WhatsApp
        </a>
    </div>
</div>
<?php endwhile; ?>
</div>

<?php
$count = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM items"));
if($count === 0):
?>
<div class="empty">
    <div class="empty-icon">📭</div>
    <p>No items listed yet. Be the first to report one!</p>
</div>
<?php endif; ?>

<script>
function filterCards(type, btn){
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.card').forEach(card => {
        card.style.display = (type === 'all' || card.dataset.type === type) ? '' : 'none';
    });
}
</script>
</body>
</html>
