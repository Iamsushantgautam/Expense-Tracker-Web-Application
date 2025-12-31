<?php
session_start();
require_once 'db.php';
requireLogin();
$userId = $_SESSION['user_id'];

// Fetch user
$stmt = $pdo->prepare("SELECT id, name, email, profile_pic, monthly_budget FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Monthly budget from database or default
$monthly_budget = isset($user['monthly_budget']) ? (float)$user['monthly_budget'] : 0.00;

// Update budget if form submitted
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['monthly_budget'])){
    $monthly_budget = (float) $_POST['monthly_budget'];
    $stmt = $pdo->prepare("UPDATE users SET monthly_budget = ? WHERE id = ?");
    $stmt->execute([$monthly_budget, $userId]);
    header('Location: dashboard.php');
    exit;
}

// Totals by category
$stmt = $pdo->prepare("SELECT category, SUM(amount) as total FROM expenses WHERE user_id = ? GROUP BY category");
$stmt->execute([$userId]);
$categoryTotals = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Total expenses
$totalExpenses = array_sum(array_column($categoryTotals, 'total'));

// Recent expenses
$stmt = $pdo->prepare("SELECT * FROM expenses WHERE user_id = ? ORDER BY date DESC LIMIT 50");
$stmt->execute([$userId]);
$recent = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard Expense Tracker</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@1.3.1/dist/chartjs-plugin-annotation.min.js"></script>
<link rel="stylesheet" href="assets/css/style.css">

<style>
* {
    box-sizing:border-box;
}

/* Full layout */
body {
    margin:0;
    font-family: Arial, sans-serif;
    background:#f3f4f6;
    overflow-x:hidden;
}

/* Mobile header for drawer */
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
    justify-content:space-between;
    padding:0 12px 0 16px;
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

/* Header profile for mobile only */
.header-profile{
    display:none;
    align-items:center;
    gap:8px;
}
.header-profile img{
    width:32px;
    height:32px;
    border-radius:999px;
    object-fit:cover;
    border:2px solid #fff;
}
.header-profile span{
    font-size:13px;
    font-weight:500;
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

/* Main area */
.main {
    margin-left:260px;
    padding:20px;
    transition:0.3s;
}
.card {
    background:#fff;
    padding:20px;
    border-radius:12px;
    margin-bottom:20px;
    box-shadow:0 8px 20px rgba(0,0,0,0.08);
    animation: fadeIn 1s ease;
}

@keyframes fadeIn {
    0%{opacity:0;transform:translateY(-20px);}
    100%{opacity:1;transform:translateY(0);}
}
.grid-two {
    display:grid;
    grid-template-columns:1fr 300px;
    gap:16px;
}

.table {
    width:100%;
    border-collapse:collapse;
}
.table th,
.table td {
    padding:10px;
    border-bottom:1px solid #e5e7eb;
    text-align:left;
    font-size:14px;
}
.table th {
    background:#f9fafb;
    font-weight:600;
}

.filter-box {
    background:#fff;
    padding:15px 20px;
    border-radius:12px;
    box-shadow:0 8px 20px rgba(0,0,0,0.08);
    margin-bottom:20px;
    display:flex;
    gap:12px;
    flex-wrap:wrap;
    align-items:center;
}

.filter-box select,
.filter-box input {
    padding:10px 12px;
    border-radius:8px;
    border:1px solid #d1d5db;
    font-size:14px;
    transition:0.2s;
}
.filter-box button {
    padding:10px 18px;
    border:none;
    border-radius:8px;
    background:#2563eb;
    color:#fff;
    cursor:pointer;
    font-weight:600;
    transition:0.3s;
}

.add-btn {
    padding:8px 16px;
    background:#4a90e2;
    color:#fff;
    border-radius:8px;
    font-weight:600;
    text-decoration:none;
    transition:0.3s;
    font-size:14px;
}

/* Dark mode */
#themeToggleBox {
    display:flex;
    align-items:center;
    justify-content:center;
    width:36px;
    height:36px;
    border-radius:8px;
    background:#f9fafb;
    cursor:pointer;
    font-size:18px;
    box-shadow:0 4px 12px rgba(0,0,0,0.1);
    transition:0.3s;
}
body.dark {
    background: #111827;
    color: #f9fafb;
}
body.dark .card,
body.dark .filter-box,
body.dark .table th,
body.dark .table td {
    background:#1f2937;
    color:#f9fafb;
    border-color:#374151;
}
body.dark .table th {
    background:#111827;
}
body.dark .sidebar a {
    background:#1f2937;
    color:#fff;
}

/* Budget popup */
.budget-popup-overlay{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.45);
    display:none;
    align-items:center;
    justify-content:center;
    z-index:1300;
}
.budget-popup{
    background:#fff;
    padding:20px;
    border-radius:12px;
    max-width:340px;
    width:90%;
    box-shadow:0 12px 30px rgba(0,0,0,0.3);
    text-align:center;
}
.budget-popup h3{
    margin-top:0;
    margin-bottom:8px;
}
.budget-popup button{
    margin-top:12px;
    padding:8px 14px;
    border:none;
    border-radius:8px;
    background:#ef4444;
    color:#fff;
    cursor:pointer;
    font-weight:600;
}
body.dark .budget-popup{
    background:#1f2937;
    color:#f9fafb;
}

/* Small laptop range */
@media(max-width:1024px){
    .sidebar {
        width:220px;
    }
    .main {
        margin-left:220px;
        padding:16px;
    }
    .grid-two {
        grid-template-columns:1.2fr 0.8fr;
    }
}

/* Tablets and below use drawer */
@media(max-width:768px){
    body{
        background:#0f172a;
    }

    .mobile-header{
        display:flex;
    }

    .sidebar{
        transform:translateX(-100%);
        width:230px;
        height:100vh;
    }
    .sidebar.open{
        transform:translateX(0);
    }

    .drawer-overlay.visible{
        opacity:1;
        pointer-events:auto;
    }

    .main {
        margin-left:0;
        padding:80px 14px 20px;
    }
    .grid-two {
        grid-template-columns:1fr;
    }

    .card {
        padding:16px;
        border-radius:10px;
    }

    .sidebar h2 {
        font-size:20px;
    }
    .sidebar a {
        font-size:13px;
        padding:9px 10px;
    }
    .profile-box img {
        width:60px;
        height:60px;
    }

    /* show header profile only on mobile and hide sidebar profile */
    .header-profile{
        display:flex;
    }
    .sidebar .profile-box{
        display:none;
    }
}

/* Mobile under 480 */
@media(max-width:480px){
    .main {
        padding:80px 10px 16px;
    }
    .add-btn {
        padding:8px 12px;
        font-size:13px;
    }

    .table-wrapper {
        width:100%;
        overflow-x:auto;
    }

    .table {
        min-width:0;
    }
    .table thead {
        display:none;
    }
    .table tr {
        display:block;
        padding:10px;
        margin-bottom:12px;
        background:#f9fafb;
        border-radius:10px;
    }
    body.dark .table tr {
        background:#111827;
    }
    .table td {
        display:flex;
        justify-content:space-between;
        padding:6px 0;
        border-bottom:none;
    }
    .table td::before {
        content: attr(data-label);
        font-weight:600;
        color:#555;
        margin-right:10px;
    }
}
</style>
</head>
<body>

<div class="budget-popup-overlay" id="budgetPopupOverlay">
    <div class="budget-popup">
        <h3>Over budget alert</h3>
        <p>You have spent more than your monthly budget. Please review your expenses.</p>
        <button id="closeBudgetPopup">Got it</button>
    </div>
</div>

<div class="mobile-header">
    <button class="menu-button" id="menuButton">☰</button>
    <span class="mobile-header-title">Expense Tracker</span>

    <!-- profile box in header, mobile only -->
    <a href="profile.php" class="header-profile" style="text-decoration:none; color:inherit;">
        <img src="<?= $user['profile_pic'] ? 'uploads/profile_pics/' . htmlspecialchars($user['profile_pic']) . '?t=' . time() : 'assets/img/default.png' ?>" alt="User">
        <span><?= htmlspecialchars($user['name']) ?></span>
    </a>
</div>

<div class="drawer-overlay" id="drawerOverlay"></div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <h2>Expense Tracker</h2>

    <a href="dashboard.php">📊 Dashboard</a>
    <a href="add_expense.php">➕ Add Expense</a>
    <a href="profile.php">👤 Profile</a>
    <a href="logout.php">🚪 Logout</a>

    <!-- profile box for desktop and tablet -->
    <div class="profile-box">
        <a href="profile.php" class="profile-link" style="text-decoration:none; color:inherit; display:inline-block;">
            <img 
                src="<?= $user['profile_pic'] 
                    ? 'uploads/profile_pics/' . htmlspecialchars($user['profile_pic']) . '?t=' . time() 
                    : 'assets/img/default.png' 
                ?>" 
                class="avatar" 
                alt="User"
            >
            <p><?= htmlspecialchars($user['name']) ?></p>
        </a>
    </div>

</div>

<!-- Main -->
<div class="main">

    <!-- Welcome Section -->
    <section class="card">
        <div style="display:flex; justify-content: space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:15px;">
            <h2 style="margin:0;">Welcome <?= htmlspecialchars($user['name']) ?></h2>

            <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                <a href="add_expense.php" class="add-btn">+ Add Expense</a>

                <form method="post" action="download_expenses.php" style="display:inline-block; margin:0;">
                    <button type="submit" class="add-btn" style="background:#10b981;">⬇ Download CSV</button>
                </form>

                <form method="post" style="display:inline-flex; gap:6px; align-items:center; margin:0;">
                    <input
                        type="number"
                        step="0.01"
                        name="monthly_budget"
                        value="<?= htmlspecialchars(number_format($monthly_budget, 2, '.', '')) ?>"
                        style="width:120px; padding:6px 8px; border-radius:6px; border:1px solid #d1d5db; font-size:14px;"
                    >
                    <button type="submit" style="padding:6px 10px; font-size:14px;">Save Budget</button>
                </form>

                <div id="themeToggleBox">🌙</div>
            </div>
        </div>

        <div class="grid-two">
            <div style="min-height:260px;">
                <h3 style="margin-top:0;">Expenses by Category</h3>
                <div style="position:relative; width:100%; height:220px;">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
            <div>
                <h3 style="margin-top:0;">Stats</h3>
                <p>Total Categories <?= count($categoryTotals) ?></p>
                <p>Total Expenses ₹<?= number_format($totalExpenses, 2) ?></p>
                <p>Monthly Budget ₹<?= number_format($monthly_budget, 2) ?></p>
                <p>Remaining Balance ₹<?= number_format($monthly_budget - $totalExpenses, 2) ?></p>
            </div>
        </div>
    </section>

    <!-- Filters -->
    <div class="filter-box">
        <select id="categoryFilter">
            <option value="">All Categories</option>
            <?php foreach($categoryTotals as $c): ?>
                <option><?= htmlspecialchars($c['category']) ?></option>
            <?php endforeach; ?>
        </select>

        <select id="monthFilter">
            <option value="">All Months</option>
            <?php
            for($m = 1; $m <= 12; $m++){
                echo "<option value='$m'>".date("F", mktime(0, 0, 0, $m, 1))."</option>";
            }
            ?>
        </select>

        <div class="date-range" style="display:flex; gap:6px; align-items:center;">
            <input type="date" id="startDate" style="flex:1;">
            <span>to</span>
            <input type="date" id="endDate" style="flex:1;">
        </div>

        <button onclick="applyFilter()">Filter</button>
    </div>

    <!-- Recent Expenses -->
    <section class="card">
        <h3 style="margin-top:0;">Recent Expenses</h3>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="expenseTable">
                    <?php foreach($recent as $r): ?>
                    <tr>
                        <td data-label="Date"><?= htmlspecialchars($r['date']) ?></td>
                        <td data-label="Title"><?= htmlspecialchars($r['title']) ?></td>
                        <td data-label="Category"><?= htmlspecialchars($r['category']) ?></td>
                        <td data-label="Amount"><?= number_format($r['amount'], 2) ?></td>
                        <td data-label="Action"><a href="edit_expense.php?id=<?= $r['id'] ?>">Edit</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

</div>

<script>
// Chart data
const categoryData = <?= json_encode(array_column($categoryTotals, 'total')) ?>;
const monthly_budget = <?= json_encode($monthly_budget) ?>;
const totalExpenses = <?= json_encode($totalExpenses) ?>;

// color each bar based on its own value vs monthly budget
const barColors = [];
for(let v of categoryData){
    if(v > monthly_budget) {
        barColors.push('#d0021b');
    } else if(v > monthly_budget * 0.8) {
        barColors.push('#f5a623');
    } else {
        barColors.push('#4a90e2');
    }
}

new Chart(document.getElementById("categoryChart"), {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($categoryTotals, 'category')) ?>,
        datasets: [{
            data: categoryData,
            backgroundColor: barColors,
            borderRadius: 6
        }]
    },
    options:{
        responsive:true,
        maintainAspectRatio:false,
        plugins:{
            legend:{ display:false },
            annotation:{
                annotations:{
                    line:{
                        type:'line',
                        yMin:monthly_budget,
                        yMax:monthly_budget,
                        borderColor:'red',
                        borderWidth:2,
                        label:{ enabled:true, content:'Monthly Budget' }
                    }
                }
            }
        },
        scales:{ y:{ beginAtZero:true } }
    }
});

// Theme toggle
const themeBox = document.getElementById("themeToggleBox");
if(localStorage.getItem("theme") === "dark"){
    document.body.classList.add("dark");
    themeBox.textContent = "☀";
}
themeBox.onclick = () => {
    document.body.classList.toggle("dark");
    if(document.body.classList.contains("dark")){
        themeBox.textContent = "☀";
        localStorage.setItem("theme", "dark");
    } else {
        themeBox.textContent = "🌙";
        localStorage.setItem("theme", "light");
    }
};

// Over budget popup
document.addEventListener("DOMContentLoaded", function(){
    if(totalExpenses > monthly_budget && monthly_budget > 0){
        const popupOverlay = document.getElementById("budgetPopupOverlay");
        const closeBtn = document.getElementById("closeBudgetPopup");

        if(popupOverlay && closeBtn){
            popupOverlay.style.display = "flex";

            closeBtn.addEventListener("click", function(){
                popupOverlay.style.display = "none";
            });

            popupOverlay.addEventListener("click", function(e){
                if(e.target === popupOverlay){
                    popupOverlay.style.display = "none";
                }
            });
        }
    }
});

// Simple filter stub
function applyFilter(){
    console.log("Filter clicked");
}

// Drawer nav
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
if(drawerOverlay){
    drawerOverlay.addEventListener("click", closeDrawer);
}
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
