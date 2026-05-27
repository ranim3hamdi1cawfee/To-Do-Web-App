<?php
session_start();
require_once("auth_guard.php");
requireLogin();
require_once("database.php"); 

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    if (!isSameUserOrAdmin($id)) {
        die("Access Denied.");
    }

    $stmt = $conn->prepare("SELECT * FROM user WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if (!$user) die("User not found.");
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_user'])) {
    $id       = (int)$_POST['id'];

    if (!isSameUserOrAdmin($id)) die("Access Denied.");

    $username   = trim($_POST["username"]);
    $image_name = $_POST["current_image"];
    $motto      = trim($_POST["motto"]);

    // Handle avatar upload securely
    if (!empty($_FILES["avatar"]["name"])) {
        // FIXED: Step up two levels (out of login_logic, out of PHP) to reach root uploads folder
        $target_dir = "../../uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $new_image = time() . "_" . basename($_FILES["avatar"]["name"]);
        
        if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_dir . $new_image)) {
            $image_name = $new_image; // Save the raw filename to the database
        }
    }

    if (isAdmin()) {
        $role     = $_POST["role"]     ?? $user["role"];
        $group_id = !empty($_POST["group_id"]) ? (int)$_POST["group_id"] : null;

        $stmt2 = $conn->prepare("UPDATE user SET username=?, profile_image=?, motto=?, role=?, group_id=? WHERE id=?");
        $stmt2->bind_param("ssssii", $username, $image_name, $motto, $role, $group_id, $id);
    } else {
        $stmt2 = $conn->prepare("UPDATE user SET username=?, profile_image=?, motto=? WHERE id=?");
        $stmt2->bind_param("sssi", $username, $image_name, $motto, $id);
    }

    if ($stmt2->execute()) {
        $stmt2->close();
        header("Location: user_infos.php?id=$id");
        exit();
    }
}

// Fetch groups for admin dropdown
$groups = [];
if (isAdmin()) {
    $groups = $conn->query("SELECT id, name FROM `group` ORDER BY name")->fetch_all(MYSQLI_ASSOC);
}

// FIXED: Clean up image preview source path parsing to jump up two levels
$current_img = $user['profile_image'];
if (empty($current_img) || $current_img === 'default_avatar.png' || $current_img === 'PHP/uploads/default_avatar.png') {
    $preview_src = '../../uploads/default_avatar.png';
} else {
    $preview_src = '../../uploads/' . $current_img;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body class="bg-dark text-white">
    <?php require_once("../welcome/navbar.php"); ?>
    <div class="adm" style="margin-top: 70px;">
        <h2 class="text-white mb-4">Update user infos</h2>
        <div class="admission_form">
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($user['profile_image']); ?>">

                <div style="text-align:center; margin-bottom:15px;">
                    <img src="<?php echo htmlspecialchars($preview_src); ?>" width="70" height="70"
                         style="border-radius:50%; object-fit:cover; border:3px solid var(--primary);">
                </div>

                <div class="decor">
                    <label class="label">Username</label>
                    <input class="input" type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>
                <div class="decor">
                    <label class="label">Motto</label>
                    <input class="input" type="text" name="motto" value="<?php echo htmlspecialchars($user['motto']); ?>">
                </div>
                <div class="decor">
                    <label class="label">New Image</label>
                    <input class="input" type="file" name="avatar" accept="image/*" style="padding-top:6px;">
                </div>

                <?php if (isAdmin()): ?>
                <div class="decor">
                    <label class="label">Role</label>
                    <select class="input" name="role">
                        <option value="Regular" <?php echo $user['role'] === 'Regular' ? 'selected' : ''; ?>>Regular</option>
                        <option value="Admin"   <?php echo $user['role'] === 'Admin'   ? 'selected' : ''; ?>>Admin</option>
                    </select>
                </div>
                <div class="decor">
                    <label class="label">Work Team</label>
                    <select class="input" name="group_id">
                        <option value="">— No team —</option>
                        <?php foreach ($groups as $g): ?>
                        <option value="<?php echo $g['id']; ?>" <?php echo $user['group_id'] == $g['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($g['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="decor" style="justify-content: center;">
                    <button type="submit" name="update_user" class="btn btn-primary px-4">Update</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>