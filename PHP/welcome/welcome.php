<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_logic/login.php");
    exit();
}
require_once("../login_logic/database.php");

$user_id = $_SESSION['user_id'];

// Fetch user data
$stmt = $conn->prepare("SELECT username, profile_image, motto FROM user WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$default_img = 'default_avatar.png'; 
$has_custom_img = (!empty($user['profile_image']) && $user['profile_image'] !== $default_img);


$display_img = $has_custom_img ? '../../uploads/' . $user['profile_image'] : null;

$hour = (int)date('H');
$greeting = ($hour < 12) ? "Good morning" : (($hour < 18) ? "Good afternoon" : "Good evening");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | Command Center</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body class="bg-dark">
    <?php require_once("navbar.php"); ?>

    <main class="dash-container">
        <div class="avatar-section">
                <div class="avatar-main avatar-initial">
                    <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                </div>
        </div>

        <header style="text-align: center;">
            <span style="color: var(--muted); font-size: 1rem; text-transform: uppercase; letter-spacing: 1px;"><?php echo $greeting; ?>,</span>
            <h1 style="font-family: 'Rajdhani', sans-serif; font-size: 3rem; color: #fff; font-weight: bold; margin: 0;">
                <?php echo htmlspecialchars($user['username']); ?>
            </h1>
            <?php if (!empty($user['motto'])): ?>
                <p style="color: var(--muted); font-style: italic; margin-top: 5px;">"<?php echo htmlspecialchars($user['motto']); ?>"</p>
            <?php endif; ?>
        </header>

        <div class="divider"></div>

        <div style="margin-top: 10px; width: 100%; max-width: 220px;">
            <a href="../add-task/index.php" class="cta">Resume Tasks →</a>
        </div>
    </main>

    <footer>
        <p class="footer_txt">&copy; 2026 Advanced Todo Webapp</p>
    </footer>
</body>
</html>