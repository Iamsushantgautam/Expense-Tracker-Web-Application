<?php
session_start();
require_once 'db.php';
requireLogin();
$userId = $_SESSION['user_id'];

// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = trim($_POST['name']);

    if(!empty($_FILES['profile_pic']['name'])){
        if($_FILES['profile_pic']['error'] === UPLOAD_ERR_OK){
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES['profile_pic']['tmp_name']);
            finfo_close($finfo);

            $allowedMime = ['image/jpeg','image/pjpeg','image/png','image/gif','image/webp'];
            if(!in_array(strtolower($mime), $allowedMime)){
                die("Invalid file type: $mime");
            }

            $extMap = [
                'image/jpeg'=>'jpg',
                'image/pjpeg'=>'jpg',
                'image/png'=>'png',
                'image/gif'=>'gif',
                'image/webp'=>'webp'
            ];
            $ext = $extMap[strtolower($mime)];

            $uploadDir = __DIR__ . '/uploads/profile_pics/';
            if(!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);

            $filename = uniqid() . '.' . $ext;

            if(move_uploaded_file($_FILES['profile_pic']['tmp_name'], $uploadDir . $filename)){
                $stmt = $pdo->prepare('UPDATE users SET name=?, profile_pic=? WHERE id=?');
                $stmt->execute([$name, $filename, $userId]);
            } else {
                die("Failed to upload file.");
            }
        } else {
            die("Upload error code: " . $_FILES['profile_pic']['error']);
        }
    } else {
        $stmt = $pdo->prepare('UPDATE users SET name=? WHERE id=?');
        $stmt->execute([$name, $userId]);
    }

    header('Location: dashboard.php');
    exit;
}

// Fetch user data
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Profile</title>
<link rel="stylesheet" href="assets/css/style.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap');

* {
    box-sizing:border-box;
}

body {
    margin:0;
    font-family:'Inter',sans-serif;
    background: linear-gradient(135deg,#4a90e2,#50e3c2);
    min-height:100vh;
    display:flex;
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
    z-index:1100;
    transition:transform 0.3s ease;
}
.sidebar h2 {
    font-size:22px;
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
    width:70px;
    height:70px;
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

/* Main area */
.main {
    margin-left:260px;
    width:100%;
    display:flex;
    justify-content:center;
    align-items:center;
    min-height:100vh;
    padding:20px;
}

/* Card */
.card {
    background:#fff;
    padding:40px 30px;
    border-radius:15px;
    width:100%;
    max-width:400px;
    text-align:center;
    box-shadow:0 20px 50px rgba(0,0,0,0.15);
    animation: fadeIn 1s ease;
    position:relative;
    z-index:1;
}
@keyframes fadeIn {
    0%{opacity:0;transform:translateY(-20px);}
    100%{opacity:1;transform:translateY(0);}
}

.card h2 {
    margin-bottom:25px;
    font-weight:600;
    color:#111;
}

/* Inputs */
input[type=text],
input[type=file] {
    width:100%;
    padding:12px 15px;
    margin:10px 0;
    border:1px solid #e2e8f0;
    border-radius:8px;
    font-size:15px;
    transition:0.3s;
}
input:focus {
    border-color:#4a90e2;
    box-shadow:0 0 10px rgba(74,144,226,0.3);
    outline:none;
}

/* Button */
button {
    width:100%;
    padding:12px;
    margin-top:12px;
    border:none;
    border-radius:8px;
    background:#4a90e2;
    color:#fff;
    font-size:16px;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
}
button:hover {
    background:#357ab8;
    transform: translateY(-2px);
    box-shadow:0 6px 12px rgba(0,0,0,0.15);
}

/* Avatar preview */
.avatar {
    width:120px;
    height:120px;
    border-radius:999px;
    object-fit:cover;
    margin-bottom:15px;
}

/* Small laptops */
@media(max-width:1024px){
    .sidebar {
        width:220px;
    }
    .main {
        margin-left:220px;
        padding:20px 16px;
    }
}

/* Tablets and below use drawer style */
@media(max-width:768px){
    body {
        flex-direction:column;
        align-items:stretch;
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
        min-height:auto;
        padding:80px 16px 20px;
        justify-content:center;
    }
    .card{
        max-width:420px;
        padding:32px 22px;
    }
}

/* Small phones */
@media(max-width:480px){
    body {
        padding-bottom:20px;
    }
    .sidebar {
        padding:18px 14px;
    }
    .sidebar h2 {
        font-size:18px;
    }
    .card{
        max-width:100%;
        padding:26px 18px;
        border-radius:12px;
    }
    input[type=text],
    input[type=file],
    button{
        font-size:14px;
        padding:10px 12px;
    }
    .avatar{
        width:100px;
        height:100px;
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
    <a href="profile.php">👤 Profile</a>
    <a href="logout.php">🚪 Logout</a>

    <div class="profile-box">
        <img src="<?= $user['profile_pic'] ? 'uploads/profile_pics/' . htmlspecialchars($user['profile_pic']) . '?t=' . time() : 'assets/img/default.png' ?>" class="avatar" alt="User">
        <p><?= htmlspecialchars($user['name']) ?></p>
    </div>
</div>

<!-- Main -->
<div class="main">
    <div class="card">
        <h2>Profile</h2>
        <img src="<?= $user['profile_pic'] ? 'uploads/profile_pics/' . htmlspecialchars($user['profile_pic']) . '?t=' . time() : 'assets/img/default.png' ?>" alt="profile" class="avatar">
        <form method="post" enctype="multipart/form-data">
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" placeholder="Your Name" required>
            <input type="file" name="profile_pic" accept="image/*">
            <button type="submit">Save</button>
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
