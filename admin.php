<?php
session_start();
if(!isset($_SESSION['admin'])){
    header("Location: index.php");
    exit();
}
include "db.php";

$result = mysqli_query($conn, "SELECT * FROM items ORDER BY id DESC");
$total  = mysqli_num_rows($result);
$lost   = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM items WHERE type='lost'"));
$found  = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM items WHERE type='found'"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Panel – Lost & Found</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .page-header { padding-top: 52px; }
        .stats-row {
            display: flex; justify-content: center; gap: 20px;
            padding: 0 24px 36px; flex-wrap: wrap;
        }
        .stat-pill {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: 14px; padding: 18px 32px; text-align: center; min-width: 120px;
        }
        .stat-num {
            font-family: 'Syne', sans-serif; font-size: 2rem; font-weight: 800;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        .stat-label { font-size: .75rem; color: var(--muted); text-transform: uppercase; letter-spacing: .07em; margin-top: 2px; }

        .admin-table-wrap { max-width: 900px; margin: 0 auto; padding: 0 24px 60px; }
        table { width: 100%; border-collapse: collapse; }
        th {
            font-family: 'Syne', sans-serif; font-size: .75rem; text-transform: uppercase;
            letter-spacing: .07em; color: var(--muted); font-weight: 600;
            padding: 10px 14px; text-align: left; border-bottom: 1px solid var(--border);
        }
        td {
            padding: 14px 14px; font-size: .875rem; color: var(--text);
            border-bottom: 1px solid var(--border); vertical-align: middle;
        }
        tr:hover td { background: var(--surface2); }
        td img { width: 52px; height: 44px; object-fit: cover; border-radius: 8px; }
        .del-btn {
            padding: 6px 14px; font-size: .75rem; border-radius: 8px; width: auto;
            background: rgba(255,101,132,0.12); color: var(--lost);
            border: 1px solid rgba(255,101,132,0.3); cursor: pointer;
            transition: background .2s, transform .15s;
        }
        .del-btn:hover { background: rgba(255,101,132,0.28); transform: none; }
    </style>
</head>
<body>

<nav class="navbar">
    <a href="index.php" class="brand">Lost<span>&</span>Found</a>
    <div class="nav-links">
        <a href="view.php">View Items</a>
        <a href="admin.php" style="color:var(--text)">Admin</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>
</nav>

<div class="page-header">
    <h1>⚙️ Admin Panel</h1>
    <p>Manage all listings on the platform</p>
</div>

<div class="stats-row">
    <div class="stat-pill"><div class="stat-num"><?php echo $total; ?></div><div class="stat-label">Total Items</div></div>
    <div class="stat-pill"><div class="stat-num" style="background:linear-gradient(135deg,var(--lost),#ff8fab);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text"><?php echo $lost; ?></div><div class="stat-label">Lost</div></div>
    <div class="stat-pill"><div class="stat-num" style="background:linear-gradient(135deg,var(--found),#38bdf8);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text"><?php echo $found; ?></div><div class="stat-label">Found</div></div>
</div>

<div class="admin-table-wrap">
    <?php if($total === 0): ?>
    <div class="empty"><div class="empty-icon">📭</div><p>No items yet.</p></div>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Photo</th>
                <th>Item</th>
                <th>Type</th>
                <th>Location</th>
                <th>Phone</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $result = mysqli_query($conn, "SELECT * FROM items ORDER BY id DESC");
        while($row = mysqli_fetch_assoc($result)):
        ?>
        <tr>
            <td>
                <?php if(!empty($row['image']) && file_exists($row['image'])): ?>
                    <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="">
                <?php else: ?>
                    <div style="width:52px;height:44px;background:var(--surface2);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1.2rem"><?php echo $row['type']==='lost'?'🔴':'🟢'; ?></div>
                <?php endif; ?>
            </td>
            <td><?php echo htmlspecialchars($row['item_name']); ?></td>
            <td><span class="type <?php echo $row['type']; ?>"><?php echo ucfirst($row['type']); ?></span></td>
            <td><?php echo htmlspecialchars($row['location']); ?></td>
            <td><?php echo htmlspecialchars($row['phone']); ?></td>
            <td>
                <a href="delete.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete this item?')">
                    <button class="del-btn">🗑 Delete</button>
                </a>
            </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

</body>
</html>
