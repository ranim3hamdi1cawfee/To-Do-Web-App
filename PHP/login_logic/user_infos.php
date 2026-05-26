<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_logic/login.php");
    exit();
}

require_once("db.php"); 
require_once("auth_guard.php"); 

if (!isset($_GET['id'])) {
    header("Location: ../welcome/welcome.php");
    exit();
}

$id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT * FROM user WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("User not found.");
}

// Format human-readable text strings from raw dates
$joined_date = date("F Y", strtotime($user['created_at']));
$birthday_formatted = !empty($user['birthday']) ? date("F d, Y", strtotime($user['birthday'])) : 'Not Specified';

// --- FETCH WORK TEAM GROUP NAME ---
$group_name = null;
if (!empty($user['group_id'])) {
    // Backticks are critical because GROUP is a reserved SQL clause statement!
    $gstmt = $conn->prepare("SELECT name FROM `group` WHERE id = ?");
    $gstmt->bind_param("i", $user['group_id']);
    $gstmt->execute();
    $grow = $gstmt->get_result()->fetch_assoc();
    $group_name = $grow['name'] ?? null;
    $gstmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile | <?php echo htmlspecialchars($user['username']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body class="bg-dark text-white">
    <?php require_once("../welcome/navbar.php"); ?>
    
    <div class="adm" style="margin-top: 70px;">
        <h2 class="text-white mb-4" style="font-size:50px; font-weight: bold; text-align: center;">User Profile</h2>
        
        <div class="container-box" style="max-width: 500px; margin: 0 auto;">
            
            <div class="decor">
                <label class="label">User Name</label>
                <span style="flex:1; padding-left:10px; font-weight: 600;"><?php echo htmlspecialchars($user['username']); ?></span>
            </div>

            <div class="decor">
                <label class="label">Account Role</label>
                <span style="flex:1; padding-left:10px; color: var(--primary); font-weight: bold;">
                    🛡️ <?php echo htmlspecialchars($user['role']); ?>
                </span>
            </div>

            <div class="decor">
                <label class="label">Birthday</label>
                <span style="flex:1; padding-left:10px;"><?php echo $birthday_formatted; ?></span>
            </div>

            <?php if ($group_name): ?>
            <div class="decor">
                <label class="label">Work Team</label>
                <span style="flex:1; padding-left:10px; font-weight: 500; color: #38bdf8;"><?php echo htmlspecialchars($group_name); ?></span>
            </div>
            <?php endif; ?>

            <div class="decor">
                <label class="label">Motto</label>
                <span style="flex:1; padding-left:10px; font-style: italic;">"<?php echo htmlspecialchars($user['motto']); ?>"</span>
            </div>

            <div class="decor">
                <label class="label">Member Since</label>
                <span style="flex:1; padding-left:10px;"><?php echo $joined_date; ?></span>
            </div>

            <div class="decor">
                <label class="label">Current Streak</label>
                <span style="flex:1; padding-left:10px; font-weight:bold; color: crimson;">
                    🔥 <?php echo $user['current_streak']; ?> Days
                </span>
            </div>

            <div class="decor" style="justify-content:center; margin-top:20px;">
                <a href="../welcome/welcome.php" class="btn btn-secondary px-4">Return to Dashboard</a>
            </div>

            <?php if (isSameUserOrAdmin((int)$user['id'])): ?>
                <div class="decor" style="justify-content:center; margin-top:10px;">
                    <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-primary px-4">Edit Profile</a>
                </div>
                <div class="decor" style="justify-content:center; margin-top:10px;">
                    <a href="delete_user.php?id=<?php echo $user['id']; ?>" 
                       class="btn btn-danger px-4" 
                       onclick="return confirm('Are you completely sure you want to permanently delete this user account?')">
                       Delete User Account
                    </a>
                </div>
            <?php endif; ?>

        </div>
    </div>
</body>
</html>