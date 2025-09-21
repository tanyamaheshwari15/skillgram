<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$loggedInUserId = $_SESSION['user_id'];

// If id is provided in URL, view that user’s profile, else show your own
$profileId = isset($_GET['id']) ? intval($_GET['id']) : $loggedInUserId;

// Fetch user info
$stmt = $conn->prepare("SELECT name, profile_pic, bio FROM users WHERE id = ?");
$stmt->bind_param("i", $profileId);
$stmt->execute();
$stmt->bind_result($name, $profile_pic, $bio);
$stmt->fetch();  
$stmt->close();  

if (!$name) {
    echo "User not found!";
    exit();
}

if (!$bio) $bio = '🌸 BTech CSE Student | 🚀 Learning Web Dev | 💡 Sharing daily progress';
if (!$profile_pic) $profile_pic = 'default.jpg';

// Count posts
$stmtPosts = $conn->prepare("SELECT COUNT(*) FROM posts WHERE user_id = ?");
$stmtPosts->bind_param("i", $profileId);
$stmtPosts->execute();
$stmtPosts->bind_result($totalPosts);
$stmtPosts->fetch();
$stmtPosts->close();

// Count followers
$stmtFollowers = $conn->prepare("SELECT COUNT(*) FROM followers WHERE user_id = ?");
$stmtFollowers->bind_param("i", $profileId);
$stmtFollowers->execute();
$stmtFollowers->bind_result($totalFollowers);
$stmtFollowers->fetch();
$stmtFollowers->close();

// Count following
$stmtFollowing = $conn->prepare("SELECT COUNT(*) FROM followers WHERE follower_id = ?");
$stmtFollowing->bind_param("i", $profileId);
$stmtFollowing->execute();
$stmtFollowing->bind_result($totalFollowing);
$stmtFollowing->fetch();
$stmtFollowing->close();

// Fetch posts
$stmtPostsList = $conn->prepare("SELECT title FROM posts WHERE user_id = ? ORDER BY created_at ASC");
$stmtPostsList->bind_param("i", $profileId);
$stmtPostsList->execute();
$stmtPostsList->bind_result($post_title);

$posts = [];
while ($stmtPostsList->fetch()) {
    $posts[] = $post_title;
}
$stmtPostsList->close(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($name); ?> | SkillGram</title>
<link rel="stylesheet" href="css/profile.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<?php
$current_page = 'profile';
include 'navbar.php';
?>

<main>
<div class="profile-container">
  <br>
  <div class="profile-header">
    <div class="profile-pic">
      <img src="uploads/<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile Picture">
    </div>
    <div class="profile-info">
      <h2><?php echo htmlspecialchars($name); ?></h2>
      <p><b><?php echo $totalPosts; ?></b> Posts | <b><?php echo $totalFollowers; ?></b> Followers | <b><?php echo $totalFollowing; ?></b> Following</p>
      <p class="bio"><?php echo htmlspecialchars($bio); ?></p>

      <?php if ($profileId == $loggedInUserId): ?>
        <!-- Show edit only if it's your own profile -->
        <button class="edit-btn">Edit Profile</button>
      <?php endif; ?>
    </div>
  </div>

  <h3 class="section-title">Skill Progress</h3>
  <div class="posts-grid">
    <?php
    if (count($posts) > 0) {
        $day = 1;
        foreach ($posts as $title) {
            echo '<div class="post-card">🏅 Day ' . $day . ' - ' . htmlspecialchars($title) . '</div>';
            $day++;
        }
    } else {
        echo '<p>No skill updates yet.</p>';
    }
    ?>
  </div>
</div>
</main>

<?php if ($profileId == $loggedInUserId): ?>
<!-- Edit Profile Modal -->
<div id="editProfileModal" class="modal">
  <div class="modal-content">
    <span class="close-btn">&times;</span>
    <h2>Edit Profile</h2>
    <form action="update_profile.php" method="POST" enctype="multipart/form-data">
      <label>Name:</label>
      <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
      
      <label>Bio:</label>
      <textarea name="bio"><?php echo htmlspecialchars($bio); ?></textarea>
      
      <label>Profile Picture:</label>
      <input type="file" name="profile_pic" accept="image/*">
      
      <button type="submit">Save Changes</button>
    </form>
  </div>
</div>

<script>
const modal = document.getElementById("editProfileModal");
const btn = document.querySelector(".edit-btn");
const span = document.querySelector(".close-btn");

if (btn) {
  btn.onclick = () => modal.style.display = "block";
}
if (span) {
  span.onclick = () => modal.style.display = "none";
}
window.onclick = (e) => { if(e.target == modal) modal.style.display = "none"; };
</script>
<?php endif; ?>

</body>
</html>
