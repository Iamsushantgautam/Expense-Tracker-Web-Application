<?php
session_start();
require_once 'db.php';
requireLogin();
$userId = $_SESSION['user_id'];

// Fetch user info for sidebar
$stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// simple search and filter
$where = 'user_id = ?';
$params = [$userId];
if(!empty($_GET['q'])){
    $where .= ' AND (title LIKE ? OR category LIKE ?)';
    $params[] = '%'.$_GET['q'].'%';
    $params[] = '%'.$_GET['q'].'%';
}
if(!empty($_GET['from']) && !empty($_GET['to'])){
    $where .= ' AND date BETWEEN ? AND ?';
    $params[] = $_GET['from'];
    $params[] = $_GET['to'];
}

$stmt = $pdo->prepare('SELECT * FROM expenses WHERE '.$where.' ORDER BY date DESC');
$stmt->execute($params);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>All Expenses Expense Tracker</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}
body{
    font-family:'Inter',sans-serif;
    background: linear-gradient(135deg,#4a90e2,#50e3c2);
    min-height:100vh;
    display:flex;
    color:#333;
    overflow-x:hidden;
}

/* mobile header for drawer toggle */
.mobile-header{
    display:none;
    position:fixed;
    top:0;
    left:0;
    right:0;
    height:56px;
    background:#111827;
    color:#fff;
    z-index:1200;
    display:flex;
    align-items:center;
    padding:0 16px;
    gap:10px;
}
.mobile-header-title{
    font-size:18px;
    font-weight:600;
}
.menu-button{
    width:32px;
    height:32px;
    border-radius:8px;
    border:none;
    background:#1f2937;
    color:#fff;
    font-size:20px;
    display:flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
}

/* Sidebar */
.sidebar {
    width:260px;
    height:100vh;
    background:#111827;
    color:#fff;
    position:fixed;
    top:0;
    left:0;
    display:flex;
    flex-direction:column;
    padding:30px 20px;
    z-index:1100;
    transition:transform 0.3s ease;
}
.sidebar h2 {
    font-size:22px;
    margin-bottom:30px;
    font-weight:700;
}
.sidebar a {
    color:#fff;
    padding:12px 16px;
    margin-bottom:12px;
    border-radius:8px;
    text-decoration:none;
    display:block;
    font-weight:500;
    background:#1f2937;
    transition:0.3s;
    font-size:14px;
}
.sidebar a:hover {
    background:#374151;
}
.profile-box {
    margin-top:auto;
    text-align:center;
}
.profile-box img {
    width:80px;
    height:80px;
    border-radius:50%;
    border:2px solid #fff;
    object-fit:cover;
    margin-bottom:10px;
}
.profile-box p {
    font-weight:600;
    font-size:16px;
}

/* overlay for drawer */
.drawer-overlay{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.4);
    z-index:1050;
    opacity:0;
    pointer-events:none;
    transition:opacity 0.3s ease;
}

/* Main wrapper */
.main-wrapper {
    flex:1;
    margin-left:260px;
    display:flex;
    justify-content:center;
    align-items:flex-start;
    padding:40px;
}

/* Card */
.card {
    background:#fff;
    padding:30px 25px;
    border-radius:20px;
    width:100%;
    max-width:900px;
    box-shadow:0 20px 50px rgba(0,0,0,0.15);
}

/* Heading */
.card h2 {
    margin-bottom:18px;
    font-size:22px;
}

/* Filter form */
.filter {
    display:flex;
    flex-wrap:wrap;
    gap:10px;
    margin-bottom:20px;
}
.filter input {
    flex:1;
    min-width:160px;
    padding:10px 12px;
    border-radius:12px;
    border:1px solid #d1d5db;
    font-size:14px;
    transition: all 0.3s;
}
.filter input:focus {
    border-color:#4a90e2;
    box-shadow:0 4px 12px rgba(74,144,226,0.3);
    outline:none;
}
.filter button {
    padding:10px 20px;
    border:none;
    border-radius:12px;
    background:#4a90e2;
    color:#fff;
    cursor:pointer;
    font-weight:600;
    transition:0.3s;
    white-space:nowrap;
}
.filter button:hover {
    background:#357ab8;
    transform:translateY(-2px);
}

/* Table wrapper for horizontal scroll on small screens */
.table-wrapper{
    width:100%;
    overflow-x:auto;
}

/* Table */
.table {
    width:100%;
    border-collapse:collapse;
    min-width:600px;
}
.table th,
.table td {
    padding:12px;
    border-bottom:1px solid #e5e7eb;
    text-align:left;
    font-size:14px;
}
.table th {
    background:#f9fafb;
    font-weight:600;
}
.table tr:hover {
    background:#f1f5f9;
}

/* Action link */
.table a {
    color:#4a90e2;
    text-decoration:none;
    font-weight:500;
}
.table a:hover {
    text-decoration:underline;
}

/* Laptops */
@media(max-width:1024px){
    .sidebar{
        width:230px;
        padding:24px 16px;
    }
    .main-wrapper{
        margin-left:230px;
        padding:30px 24px;
    }
}

/* Tablets and below use drawer style */
@media(max-width:768px){
    body{
        flex-direction:column;
    }

    .mobile-header{
        display:flex;
    }

    .sidebar{
        transform:translateX(-100%);
        padding:24px 16px;
    }
    .sidebar.open{
        transform:translateX(0);
    }

    .drawer-overlay.visible{
        opacity:1;
        pointer-events:auto;
    }

    .main-wrapper{
        margin-left:0;
        padding:80px 16px 20px;
        justify-content:flex-start;
    }
    .card{
        border-radius:16px;
        padding:24px 18px;
    }

    .sidebar h2{
        font-size:18px;
        margin-bottom:20px;
    }
    .sidebar a{
        font-size:13px;
        padding:10px 12px;
    }
    .profile-box img{
        width:60px;
        height:60px;
    }
    .profile-box p{
        font-size:13px;
    }
}

/* Small phones */
@media(max-width:480px){
    .card{
        padding:20px 14px;
        border-radius:14px;
    }
    .card h2{
        font-size:20px;
    }
    .filter{
        flex-direction:column;
        align-items:stretch;
    }
    .filter input{
        width:100%;
    }
    .filter button{
        width:100%;
        text-align:center;
    }
    .table{
        min-width:0;
    }
    .table thead{
        display:none;
    }
    .table tr{
        display:block;
        padding:10px;
        margin-bottom:10px;
        background:#f9fafb;
        border-radius:10px;
    }
    .table td{
        display:flex;
        justify-content:space-between;
        border-bottom:none;
        padding:6px 0;
    }
    .table td::before{
        content: attr(data-label);
        font-weight:600;
        color:#555;
        margin-right:10px;
    }
}
</style>
</head>
<body>

<div class="mobile-header">
    <button class="menu-button" id="menuButton">☰</button>
    <span class="mobile-header-title">Expense Tracker</span>
</div>

<div class="drawer-overlay" id="drawerOverlay"></div>

<!-- Sidebar drawer -->
<div class="sidebar" id="sidebar">
    <h2>Expense Tracker</h2>
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="add_expense.php">➕ Add Expense</a>
    <a href="profile.php">👤 Profile</a>
    <a href="logout.php">🚪 Logout</a>

    <div class="profile-box">
        <img src="<?= $user['profile_pic'] ? 'uploads/profile_pics/' . htmlspecialchars($user['profile_pic']) : 'assets/img/default.png' ?>" alt="User">
        <p><?= htmlspecialchars($user['name']) ?></p>
    </div>
</div>

<!-- Main Wrapper -->
<div class="main-wrapper">
    <div class="card">
        <h2>All Expenses</h2>
        <form method="get" class="filter">
            <input name="q" placeholder="Search title or category" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            <input name="from" type="date" value="<?= htmlspecialchars($_GET['from'] ?? '') ?>">
            <input name="to" type="date" value="<?= htmlspecialchars($_GET['to'] ?? '') ?>">
            <button type="submit">Filter</button>
        </form>

        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($items as $it): ?>
                    <tr>
                        <td data-label="Date"><?= htmlspecialchars($it['date']) ?></td>
                        <td data-label="Title"><?= htmlspecialchars($it['title']) ?></td>
                        <td data-label="Category"><?= htmlspecialchars($it['category']) ?></td>
                        <td data-label="Amount"><?= number_format($it['amount'],2) ?></td>
                        <td data-label="Actions"><a href="edit_expense.php?id=<?= $it['id'] ?>">Edit</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<script>
const sidebar = document.getElementById("sidebar");
const menuButton = document.getElementById("menuButton");
const drawerOverlay = document.getElementById("drawerOverlay");
const sidebarLinks = sidebar.querySelectorAll("a");

function openDrawer(){
    sidebar.classList.add("open");
    drawerOverlay.classList.add("visible");
}
function closeDrawer(){
    sidebar.classList.remove("open");
    drawerOverlay.classList.remove("visible");
}

menuButton.addEventListener("click", function(){
    if(sidebar.classList.contains("open")){
        closeDrawer();
    } else {
        openDrawer();
    }
});

drawerOverlay.addEventListener("click", closeDrawer);

sidebarLinks.forEach(function(link){
    link.addEventListener("click", function(){
        if(window.innerWidth <= 768){
            closeDrawer();
        }
    });
});
</script>

</body>
</html>
