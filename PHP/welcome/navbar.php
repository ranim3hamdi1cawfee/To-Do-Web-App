<?php
require_once("../login_logic/database.php");
$nav_user_id = $_SESSION['user_id'];
$nav_query = $conn->prepare("SELECT username, profile_image FROM user WHERE id = ?");
$nav_query->bind_param("i", $nav_user_id);
$nav_query->execute();
$nav_result = $nav_query->get_result()->fetch_assoc();

$nav_img = $nav_result['profile_image'];
$is_default = (empty($nav_img) || $nav_img === 'default_avatar.png');
$nav_img_src = $is_default ? '../../uploads/default_avatar.png' : '../../uploads/' . $nav_img;
?>
<link rel="stylesheet" href="../../css/style.css">
<nav>
    <label class="logo">To_Do Web Application</label>
    <ul>
        <li><a href="../welcome/welcome.php">Home</a></li>
        <li><a href="../add-task/index.php">Add task</a></li>
        <li><a href="../list_task/tasks.php">Tasks list</a></li>
        <li><a href="../Statistics/statistics.php">Stats</a></li>
        <li><a href="../login_logic/logout.php">Logout</a></li>

        <li class="nav-avatar">
            <a href="../login_logic/user_infos.php?id=<?php echo $_SESSION['user_id']; ?>">
                <img src="<?php echo htmlspecialchars($nav_img_src); ?>"
                     width="34" height="34"
                     style="border-radius:50%; object-fit:cover; border:2px solid var(--primary); vertical-align:middle;"
                     title="<?php echo htmlspecialchars($nav_result['username']); ?>">
            </a>
        </li>
    </ul>
</nav>