<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['accountType'])) {
    header("Location: login.php");
    exit;
}
?>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Montserrat', sans-serif;
}
body {
    display: flex;
    background-color:rgba(1, 26, 58, 0.99);
}
.sidebar {
    width: 250px;
    background-color:rgb(255, 255, 255);
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
    min-height: 100vh;
    padding: 20px;
    display: flex;
    flex-direction: column;
    border-right: 1px solid #ddd;
    transition: width 0.3s ease;
}
.menu {
    list-style: none;
}
.menu li {
    padding: 10px;
    cursor: pointer;
    display: flex;
    align-items: center;
}
.menu li a {
    text-decoration: none;
    color: rgba(1, 26, 58, 0.99);
    display: flex;
    align-items: center;
    justify-content: start;
    padding: 14px 14px;
    border-radius: 6px;
    width: 100%;
    transition: all 0.2s ease-in-out;
}

.sidebar.collapsed .menu li a {
    justify-content: center; /* center horizontally */
    padding: 14px;
    text-align: center;
    gap: 10px; /* spacing between icon and text */
}

.menu li a i {
    margin-right: 12px;
    font-size: 16px;
}
.sidebar:not(.collapsed) .menu li:hover a,
.sidebar:not(.collapsed) .menu li a.active {
    background-color: #f3f3f3;
    color:rgb(16, 69, 205);
    font-weight: 600;
    margin-right: 12px;
    font-size: 16px;
}
.sidebar.collapsed {
    width: 100px;
}
.sidebar.collapsed .menu li a span {
    display: none;
}
.sidebar.collapsed .menu li a i {
    margin: 0 auto;
}
.sidebar.collapsed .menu li a.active {
    color:rgb(16, 69, 205);
}

.hamburger {
    cursor: pointer;
    padding: 20px;
    text-align: right;
}
.hamburger img {
    width: 30px;
    height: auto;
}

.sidebar-header {
    display: flex;
    align-items: center;
    justify-content: space-evenly;
    padding: 30px 10px;
    margin-bottom: 30px;
}
.sidebar-header img {
    width: 30px;
    cursor: pointer;
}
.sidebar.collapsed .sidebar-header h1 {
    display: none;
}
.sidebar.collapsed .sidebar-header img {
    content: url('img/logo.png');
    width: 40px;
}

.brand {
    text-align: center;
    margin: 20px 0 50px;
}
@import url('https://fonts.googleapis.com/css2?family=Quicksand:wght@500&display=swap');

.brand-text {
    font-family: 'Quicksand', sans-serif;
    font-size: 22px;
    font-weight: 800;
    color:rgb(16, 69, 205); /* Elegant blue */
}



.brand-logo {
    width: 50px;
    height: auto;
    display: none;
    margin: 0 auto;
}
.sidebar.collapsed .brand-text {
    display: none;
}
.sidebar.collapsed .brand-logo {
    display: block;
}

.main-content {
    flex: 1;
    padding: 20px;
}
.dashboard-header {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 20px;
    padding-bottom: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid rgb(244, 244, 244);
    color:rgb(255, 255, 255);
}
.header-right {
    display: flex;
    align-items: center;
}
.lupon-btn {
    background-color: #fff;
    color:rgb(16, 69, 205);
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 15px;
    cursor: pointer;
    border: 2px solid rgb(15, 1, 97);
    transition: all 0.2s ease-in-out;
}
.lupon-btn:hover {
    background-color:rgb(12, 0, 231);
    color: #fff;
}
.container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}
</style>

<div class="container">
    <div class="sidebar collapsed" id="sidebar">
        <div class="sidebar-header">
            <h1 class="brand-text" id="brandText">
            <?php echo ($_SESSION['accountType'] === 'lupon') ? 'LUPON' : 'OFFICIAL'; ?>
            </h1>
            <img src='img/menu.png' alt="Menu Icon" id="menuIcon" onclick="toggleSidebar()" />
        </div>

        <ul class="menu">
            <li><a href="index.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'class="active"' : ''; ?>><i class="fas fa-home"></i><span>Dashboard</span></a></li>
            <li><a href="cases.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'cases.php') ? 'class="active"' : ''; ?>><i class="fas fa-balance-scale"></i><span>Cases</span></a></li>
            <li><a href="reports.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'reports.php') ? 'class="active"' : ''; ?>><i class="fas fa-chart-line"></i><span>Reports</span></a></li>
            <li><a href="archive.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'archive.php') ? 'class="active"' : ''; ?>><i class="fas fa-archive"></i><span>Archive</span></a></li>
            <?php if (isset($_SESSION['accountType']) && $_SESSION['accountType'] === 'lupon'): ?>
            <li><a href="settings.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'settings.php') ? 'class="active"' : ''; ?>><i class="fas fa-cog"></i><span>Settings</span></a></li>
            <li><a href="logs.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'logs.php') ? 'class="active"' : ''; ?>><i class="fas fa-clipboard-list"></i><span>Logs</span></a></li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<script>
// Sidebar toggle functionality
function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    sidebar.classList.toggle("collapsed");

    // Save state to localStorage
    const isCollapsed = sidebar.classList.contains("collapsed");
    localStorage.setItem("sidebarCollapsed", isCollapsed);
}

// Load saved state on page load
document.addEventListener("DOMContentLoaded", function() {
    const sidebar = document.getElementById("sidebar");
    const savedState = localStorage.getItem("sidebarCollapsed");

    if (savedState !== null) {
        if (savedState === "true") {
            sidebar.classList.add("collapsed");
        } else {
            sidebar.classList.remove("collapsed");
        }
    }
});
</script>
