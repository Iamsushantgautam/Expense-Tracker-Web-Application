<?php
session_start();
require_once 'db.php';
requireLogin();
$userId = $_SESSION['user_id'];

// Fetch user info for sidebar
$stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $title = trim($_POST['title']);
    $amount = (float) $_POST['amount'];
    $category = trim($_POST['category']);
    $date = $_POST['date'];
    $notes = trim($_POST['notes']);

    $stmt = $pdo->prepare('INSERT INTO expenses (user_id,title,amount,category,date,notes) VALUES (?,?,?,?,?,?)');
    $stmt->execute([$userId,$title,$amount,$category,$date,$notes]);

    header('Location: expenses.php');
    exit;
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Add Expense - Expense Tracker</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
<style>
/* Reset and Body */
* {
    margin:0;
    padding:0;
    box-sizing:border-box;
}
body {
    font-family:'Inter',sans-serif;
    background: linear-gradient(135deg,#4a90e2,#50e3c2);
    min-height:100vh;
    display:flex;
    color:#333;
    overflow-x:hidden;
}

/* Mobile header for drawer toggle */
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

/* Overlay for drawer */
.drawer-overlay{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.4);
    z-index:1050;
    opacity:0;
    pointer-events:none;
    transition:opacity 0.3s ease;
}

/* Main Wrapper */
.main-wrapper {
    flex:1;
    display:flex;
    justify-content:center;
    align-items:center;
    margin-left:260px;
    min-height:100vh;
    padding:20px;
}

/* Main Card */
.card {
    background:#fff;
    padding:40px 30px;
    border-radius:20px;
    width:100%;
    max-width:450px;
    text-align:center;
    box-shadow:0 20px 50px rgba(0,0,0,0.15);
}
.card h2 {
    margin-bottom:16px;
}

/* Inputs */
input,
textarea {
    width:100%;
    padding:14px 16px;
    margin:10px 0;
    border:1px solid #d1d5db;
    border-radius:12px;
    font-size:15px;
    transition: all 0.3s;
}
input:focus,
textarea:focus {
    border-color:#4a90e2;
    box-shadow:0 4px 12px rgba(74,144,226,0.3);
    outline:none;
}
textarea {
    min-height:80px;
    resize: vertical;
}

/* Buttons */
.btn-row {
    display:flex;
    gap:15px;
    margin-top:18px;
    flex-wrap:wrap;
    justify-content:space-between;
    align-items:center;
}
button {
    flex:1;
    padding:12px 0;
    font-size:15px;
    font-weight:600;
    border:none;
    border-radius:12px;
    cursor:pointer;
    transition: all 0.3s;
}
button.add {
    background:#4a90e2;
    color:#fff;
}
button.add:hover {
    background:#357ab8;
    transform: translateY(-2px);
}

/* Laptops */
@media(max-width:1024px){
    .sidebar{
        width:230px;
        padding:26px 18px;
    }
    .main-wrapper{
        margin-left:230px;
        padding:20px;
    }
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
        align-items:flex-start;
    }
    .card{
        max-width:460px;
        margin:0 auto;
        padding:34px 24px;
    }
}

/* Small phones */
@media(max-width:480px){
    .main-wrapper{
        padding:80px 14px 20px;
    }
    .card{
        width:100%;
        padding:28px 18px;
        border-radius:16px;
    }
    .btn-row{
        flex-direction:column;
    }
    button{
        width:100%;
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

<!-- Sidebar -->
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

<!-- Main Wrapper -->
<div class="main-wrapper">
    <div class="card">
        <h2>Add Expense</h2>
        <form method="post">
            <input type="text" name="title" placeholder="Title" required>
            <input type="number" step="0.01" name="amount" placeholder="Amount" required>
            <input type="text" name="category" placeholder="Category" required>
            <input type="date" name="date" required>
            <textarea name="notes" placeholder="Notes"></textarea>
            <div class="btn-row">
                <button type="submit" class="add">Add Expense</button>
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
