<?php
session_start();
require_once 'db.php';
requireLogin();
$userId = $_SESSION['user_id'];

if(!isset($_GET['id'])){
    header('Location: expenses.php');
    exit;
}
$id = (int)$_GET['id'];

$stmt = $pdo->prepare('SELECT * FROM expenses WHERE id = ? AND user_id = ?');
$stmt->execute([$id, $userId]);
$expense = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$expense){
    header('Location: expenses.php');
    exit;
}

// Fetch user info for sidebar
$stmt = $pdo->prepare("SELECT id, name, profile_pic FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update'){
    $title    = trim($_POST['title']);
    $amount   = (float) $_POST['amount'];
    $category = trim($_POST['category']);
    $date     = $_POST['date'];
    $notes    = trim($_POST['notes']);

    $stmt = $pdo->prepare('UPDATE expenses SET title=?, amount=?, category=?, date=?, notes=? WHERE id=? AND user_id=?');
    $stmt->execute([$title, $amount, $category, $date, $notes, $id, $userId]);
    header('Location: expenses.php');
    exit;
}
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Edit Expense</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap');

*{
    box-sizing:border-box;
}

body {
    margin:0;
    font-family:'Inter', sans-serif;
    background: linear-gradient(135deg,#4a90e2,#50e3c2);
    overflow-x:hidden;
    min-height:100vh;
    display:flex;
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
    width:240px;
    height:100vh;
    background:#111827;
    color:#fff;
    position:fixed;
    left:0;
    top:0;
    padding:20px;
    display:flex;
    flex-direction:column;
    animation: slideIn 0.8s ease;
    z-index:1100;
    transition:transform 0.3s ease;
}
.sidebar h2 {
    font-size:20px;
    margin-bottom:20px;
    font-weight:600;
}
.sidebar a {
    color:#fff;
    padding:10px 12px;
    text-decoration:none;
    border-radius:6px;
    margin-bottom:10px;
    display:block;
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
    width:60px;
    height:60px;
    border-radius:999px;
    object-fit:cover;
    border:2px solid #fff;
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

/* Main content */
.main {
    margin-left:260px;
    display:flex;
    justify-content:center;
    align-items:center;
    min-height:100vh;
    padding:20px 0;
    width:100%;
}

/* Card */
.card {
    background:#fff;
    padding:35px 30px;
    border-radius:15px;
    width:100%;
    max-width:380px;
    text-align:center;
    box-shadow:0 20px 50px rgba(0,0,0,0.15);
    position:relative;
    animation: fadeInUp 1s ease;
}

/* Animations */
@keyframes fadeInUp {
    0% { opacity:0; transform: translateY(-30px);}
    100% { opacity:1; transform: translateY(0);}
}
@keyframes slideIn {
    0% { opacity:0; transform: translateX(-50px);}
    100% { opacity:1; transform: translateX(0);}
}

/* Form */
.card h2 {
    font-size:20px;
    font-weight:600;
    margin-bottom:20px;
    color:#111;
}
input, textarea {
    width:100%;
    padding:12px 12px;
    margin:10px 0;
    border:1px solid #e2e8f0;
    border-radius:8px;
    font-size:15px;
    transition:0.3s;
}
input:focus, textarea:focus {
    border-color:#4a90e2;
    box-shadow:0 0 10px rgba(74,144,226,0.3);
    outline:none;
}
textarea { resize: vertical; min-height:70px; }

/* Buttons row */
.btn-row {
    display:flex;
    gap:10px;
    margin-top:15px;
    flex-wrap:wrap;
}
button {
    flex:1;
    padding:10px;
    border:none;
    border-radius:8px;
    font-size:14px;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
}
button.update {
    background:#4a90e2;
    color:#fff;
}
button.update:hover {
    background:#357ab8;
    transform: translateY(-2px);
    box-shadow:0 6px 12px rgba(0,0,0,0.15);
}
button.danger {
    background:#ef4444;
    color:#fff;
}
button.danger:hover {
    background:#c92a2a;
    transform: translateY(-2px);
    box-shadow:0 6px 12px rgba(0,0,0,0.15);
}

/* Background bubbles */
body::before, body::after {
    content:"";
    position:absolute;
    border-radius:50%;
    background: rgba(255,255,255,0.08);
    animation: float 8s infinite ease-in-out alternate;
}
body::before { width:180px; height:180px; top:-60px; left:-50px; }
body::after { width:280px; height:280px; bottom:-100px; right:-80px; animation-delay:3s; }

@keyframes float {
    0% { transform: translateY(0) rotate(0deg);}
    100% { transform: translateY(-25px) rotate(45deg);}
}

/* Tablets and below use drawer */
@media(max-width:768px){
    body{
        flex-direction:column;
    }

    .mobile-header{
        display:flex;
    }

    .sidebar{
        transform:translateX(-100%);
        padding:20px 16px;
    }
    .sidebar.open{
        transform:translateX(0);
    }

    .drawer-overlay.visible{
        opacity:1;
        pointer-events:auto;
    }

    .main{
        margin-left:0;
        padding:80px 16px 20px;
        align-items:flex-start;
    }
    .card{
        max-width:400px;
        margin:0 auto;
    }
}

/* Small phones */
@media(max-width:480px){
    .card {
        padding:25px 20px;
        max-width:95%;
    }
    .btn-row {
        flex-direction: column;
    }
    .sidebar {
        width:220px;
        padding:18px 14px;
    }
    .sidebar h2 {
        font-size:18px;
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

<div class="sidebar" id="sidebar">
    <h2>Expense Tracker</h2>
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="expenses.php">💰 Expenses</a>
    <a href="profile.php">👤 Profile</a>
    <a href="logout.php">🚪 Logout</a>

    <div class="profile-box">
        <img src="<?= $user['profile_pic'] ? 'uploads/profile_pics/' . htmlspecialchars($user['profile_pic']) : 'assets/img/default.png' ?>" class="avatar" alt="User">
        <p><?= htmlspecialchars($user['name']) ?></p>
    </div>
</div>

<div class="main">
    <div class="card">
        <h2>Edit Expense</h2>

        <!-- Update form -->
        <form method="post">
            <input type="hidden" name="action" value="update">
            <input name="title" value="<?= htmlspecialchars($expense['title']) ?>" placeholder="Title" required>
            <input name="amount" type="number" step="0.01" value="<?= htmlspecialchars($expense['amount']) ?>" placeholder="Amount" required>
            <input name="category" value="<?= htmlspecialchars($expense['category']) ?>" placeholder="Category" required>
            <input name="date" type="date" value="<?= htmlspecialchars($expense['date']) ?>" required>
            <textarea name="notes" placeholder="Notes"><?= htmlspecialchars($expense['notes']) ?></textarea>

            <div class="btn-row">
                <button type="submit" class="update">Update</button>

                <!-- Delete form (separate, not nested) -->
                <form method="post" action="delete_expense.php" onsubmit="return confirm('Are you sure you want to delete this expense?');" style="flex:1; margin:0;">
                    <input type="hidden" name="id" value="<?= $id ?>">
                    <button type="submit" class="danger">Delete</button>
                </form>
            </div>
        </form>
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

if(menuButton){
    menuButton.addEventListener("click", function(){
        if(sidebar.classList.contains("open")){
            closeDrawer();
        } else {
            openDrawer();
        }
    });
}

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
