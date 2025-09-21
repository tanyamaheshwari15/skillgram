<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, profile_pic FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($user_name, $user_pic);
$stmt->fetch();
$stmt->close();
?>

<header>
    <nav class="navbar">
        <div class="container">
            <!-- Sidebar Toggle Button -->
            <b class="menu-toggle" onclick="toggleSidebar()"><i class="fas fa-bars"></i></b>
            <!-- Left: Brand -->
            <b style="font-size:24px;" class="gradient-text">SkillGram</b>


            <!-- Right Side -->
            <div class="nav-right">
                <!-- Profile Avatar -->
                <a href="profile.php">
                    <img src="uploads/<?php echo htmlspecialchars($user_pic ?: 'default.png'); ?>" 
                         alt="User Avatar" class="avatar">
                </a>
                <!-- Logout -->
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i></a>
            </div>
        </div>
    </nav>
</header>

<!-- Sidebar -->
<div id="sidebar" class="sidebar">
    <ul>
        <li><a href="home.php"><i class="fas fa-home"></i> Home</a></li>
        <li><a href="leaderboard.php"><i class="fas fa-trophy"></i> Leaderboard</a></li>
        <li><a href="post.php"><i class="fas fa-plus-circle"></i> Post Skill</a></li>
        <li><a href="roadmaps.php"><i class="fas fa-route"></i> Roadmaps</a></li>
        <li><a href="projects.php"><i class="fas fa-lightbulb"></i> Projects</a></li>
        <li><a href="quests.php"><i class="fas fa-gamepad"></i> FunQuest</a></li>
        <li><a href="discussion.php"><i class="fas fa-comments"></i> Discussion</a></li>
        <li><a href="problems.php"><i class="fas fa-code"></i> Problems</a></li>
        <li><a href="practice.php"><i class="fas fa-laptop-code"></i> Practice</a></li>
        <li><a href="resources.php"><i class="fas fa-link"></i> Resources</a></li>
        <li><a href="achievements.php"><i class="fas fa-medal"></i> Achievements</a></li>
        <li><a href="learn.php"><i class="fas fa-graduation-cap"></i> Learn</a></li>
    </ul>
</div>


<style>

/* ====== Navbar ====== */
.navbar {
    background: #fff;
    padding: 12px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #ddd;
    position: sticky;
    top: 0;
    z-index: 1000;
}
.gradient-text {
  font-size: 30px;
  font-weight: bold;
  background: linear-gradient(to right, #5f9aff, #fe7bf3);
  background-clip: text;
  -webkit-text-fill-color: transparent;
}

/* Right side */
.nav-right {
    display: flex;
    align-items: center;
    gap: 12px;
}

/* Avatar */
.avatar {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    border: 2px solid #6286fbff;
}

/* Logout */
.logout-btn {
    background: #6286fbff;
    color: #fff !important;
    padding: 6px 12px;
    border-radius: 20px;
    transition: background 0.3s;
}
.logout-btn:hover {
    background: #e63e4d;
    text-decoration: none;
}

/* ====== Sidebar ====== */
.sidebar {
    position: fixed;
    left: -260px;
    top: 5%;
    width: 260px;
    height: 100%;
    background: #f8f9fa;
    padding-top: 60px;
    transition: 0.3s;
    box-shadow: 2px 0 6px rgba(0,0,0,0.1);
    z-index: 999;
}
.sidebar ul {
    list-style: none;
    padding: 0;
}
.sidebar ul li {
    padding: 15px 20px;
}
.sidebar ul li a {
    color: #333;
    text-decoration: none;
    font-size: 16px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.sidebar ul li a:hover {
    background: #e9ecef;
    border-radius: 6px;
}
.menu-toggle {
    cursor: pointer;
    font-size: 22px;
}
</style>

<script>
function toggleSidebar() {
    let sidebar = document.getElementById("sidebar");
    if (sidebar.style.left === "0px") {
        sidebar.style.left = "-260px";
    } else {
        sidebar.style.left = "0px";
    }
}
</script>
