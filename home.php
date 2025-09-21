<?php
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get logged-in user info
$stmt = $conn->prepare("SELECT name, profile_pic FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($user_name, $user_pic);
$stmt->fetch();
$stmt->close();

// Get all posts (latest first)
$posts = $conn->query("
    SELECT posts.id AS post_id, posts.content, posts.image, posts.created_at,
           users.id AS user_id, users.name AS user_name, users.profile_pic AS user_pic
    FROM posts
    JOIN users ON posts.user_id = users.id
    ORDER BY posts.created_at DESC
");

// Get suggestions (users not followed yet)
$suggestions = $conn->query("
    SELECT id, name, profile_pic
    FROM users
    WHERE id != $user_id
      AND id NOT IN (SELECT user_id FROM followers WHERE follower_id = $user_id)
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SkillGram</title>
<link rel="stylesheet" href="css/home.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<?php
$current_page = 'home';
include 'navbar.php';
?>

<main>
    <div class="container">
        <div class="content-row">
        <!-- Posts Column -->
        <div class="col-9">
            <h1 class="center-text">Build <span>Skills</span>, <u>Share Progress</u></h1>

            <div class="statuses">
                <p class="center-text"><b>🎯 What skill progress did you make today?</b></p>
            </div>

            <!-- Dynamic Post Feed -->
            <br>
            <h1><i class="fas fa-lightbulb"></i><span>Skill</span> <u>Feed</u></h1><br>
            <?php while($post = $posts->fetch_assoc()): ?>
                <div class="card">
                    <div class="top">
                        <div class="userDetails">
                            <div class="profilepic">
                                <div class="profile_img">
                                    <div class="image">
                                        <!-- Profile picture clickable -->
                                        <a href="profile.php?id=<?php echo $post['user_id']; ?>">
                                            <img src="uploads/<?php echo $post['user_pic']; ?>" alt="img">
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <!-- Username clickable -->
                            <h3>
                                <b>
                                    <a href="profile.php?id=<?php echo $post['user_id']; ?>" style="text-decoration:none; color:inherit;">
                                        <?php echo htmlspecialchars($post['user_name']); ?>
                                    </a>
                                </b>
                            </h3>
                        </div>
                        <div class="post-menu-wrap">
                            <span class="dot" onclick="toggleMenu(<?php echo $post['post_id']; ?>)"><i class="fas fa-ellipsis-h"></i></span>
                            <?php if($post['user_id'] == $user_id): ?>
                            <div id="menu-<?php echo $post['post_id']; ?>" class="post-menu" style="display:none;position:absolute;background:#fff;border:1px solid #ddd;border-radius:8px;box-shadow:0 6px 12px rgba(0,0,0,.08);">
                                <a href="edit_post.php?id=<?php echo $post['post_id']; ?>" style="display:block;padding:8px 12px;color:#333;text-decoration:none;">Edit</a>
                                <a href="delete_post.php?id=<?php echo $post['post_id']; ?>" onclick="return confirm('Delete this post?')" style="display:block;padding:8px 12px;color:#e63e4d;text-decoration:none;">Delete</a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if($post['image']): ?>  
                        <div class="imgBx center-text">
                            <img src="uploads/<?php echo $post['image']; ?>" alt="post image" style="height: 50%; width:300px;">
                        </div>
                    <?php endif; ?>

                    <div class="post-content">
                        <?php echo htmlspecialchars($post['content']); ?>
                    </div>

                    <div class="bottom">
                        <div class="actionBtns">
                            <div class="left">
                                <span class="heart">
                                    <a href="like.php?post_id=<?php echo $post['post_id']; ?>"><i class="fas fa-heart"></i> Like</a>
                                </span>
                            </div>
                        </div>
                        <div class="likes-count">
                            <?php
                            $likes = $conn->query("SELECT COUNT(*) as count FROM likes WHERE post_id={$post['post_id']}")->fetch_assoc()['count'];
                            echo $likes . ' likes';
                            ?>
                        </div>
                        <div class="postTime">                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             
                            <?php echo date('M d, Y', strtotime($post['created_at'])); ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Suggestions Column -->
        <div class="col-3">   
            <br>  
            <div class="card">
                <h4>Suggestions For You</h4><br>
                <?php while($s = $suggestions->fetch_assoc()): ?>
                    <div class="top" style="margin-bottom: 10px; justify-content: space-between;">
                        <div class="userDetails">
                            <div class="profilepic">
                                <div class="profile_img">
                                    <div class="image">
                                        <!-- Suggestion profile picture clickable -->
                                        <a href="profile.php?id=<?php echo $s['id']; ?>">
                                            <img src="uploads/<?php echo $s['profile_pic']; ?>" alt="img" height="50%" width="50%">
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <!-- Suggestion username clickable -->
                            <h3>
                                <a href="profile.php?id=<?php echo $s['id']; ?>" style="text-decoration:none; color:inherit;">
                                    <?php echo htmlspecialchars($s['name']); ?>
                                </a>
                            </h3>
                        </div>
                        <a href="follow.php?follow_id=<?php echo $s['id']; ?>" class="follow-btn" data-id="<?php echo $s['id']; ?>">Follow</a>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
        </div>
    </div>
</main>

</body>
<script>
document.querySelectorAll('.follow-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const followId = this.dataset.id;
        fetch('follow.php?follow_id=' + followId, { headers: { 'X-Requested-With': 'fetch' }})
        .then(r => r.json())
        .then(data => {
            if (data.action === 'followed') {
                this.innerText = 'Following';
            } else if (data.action === 'unfollowed') {
                this.innerText = 'Follow';
            }
        });
    });
});

function toggleMenu(id){
  const el = document.getElementById('menu-' + id);
  if (!el) return;
  el.style.display = (el.style.display === 'none' || el.style.display === '') ? 'block' : 'none';
  // Close on outside click
  const handler = (e) => {
    if (!el.contains(e.target)) {
      el.style.display = 'none';
      document.removeEventListener('click', handler);
    }
  };
  setTimeout(() => document.addEventListener('click', handler), 0);
}
</script>
</html>
