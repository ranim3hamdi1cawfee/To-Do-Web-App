<?php
session_start();
require_once("db.php");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// !! Change this to something only you know !!
define('ADMIN_SECRET_CODE', 'TF-ADMIN-2026');

$error = "";
$success = "";

$groups = [];
$group_result = $conn->query("SELECT id, name FROM `group` ORDER BY name ASC");
if ($group_result) {
    while ($row = $group_result->fetch_assoc()) {
        $groups[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username   = trim($_POST["username"]);
    $password   = $_POST["password"];
    $birthday   = $_POST["birthday"];
    $admin_code = trim($_POST["admin_code"] ?? '');
    $group_id   = !empty($_POST["group_id"]) ? (int)$_POST["group_id"] : null;

    // Determine role — only grant Admin if secret code matches
    $role = ($admin_code !== '' && $admin_code === ADMIN_SECRET_CODE) ? 'Admin' : 'Regular';

    // Wrong code provided (not blank, but also not correct) — tell the user
    if ($admin_code !== '' && $admin_code !== ADMIN_SECRET_CODE) {
        $error = "Invalid admin code.";
    } elseif (empty($username) || empty($password) || empty($birthday)) {
        $error = "All mandatory fields are required.";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        if ($group_id !== null) {
            $stmt = $conn->prepare("INSERT INTO user (username, password, birthday, role, group_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $username, $hashed, $birthday, $role, $group_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO user (username, password, birthday, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $hashed, $birthday, $role);
        }

        try {
            $stmt->execute();
            $success = "Account created successfully! You can now <a href='login.php'>login</a>.";
        } catch (mysqli_sql_exception $e) {
            if ($conn->errno === 1062) {
                $error = "Username already taken. Please choose another.";
            } else {
                $error = "Something went wrong. Try again.";
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body class="login-page">
    <div class="container-box" style="max-width: 450px; margin-top: 40px; margin-bottom: 40px;">
        <h1 class="title">Registration</h1>

        <div id="error-box" class="alert alert-danger" style="display:<?php echo $error ? 'flex' : 'none'; ?>; justify-content:space-between; align-items:center;">
            <span><?php echo $error; ?></span>
            <span onclick="document.getElementById('error-box').style.display='none'" style="cursor:pointer; font-size:18px;">&times;</span>
        </div>

        <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Enter your desired username" required>
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <div class="input-group">
                <label>Date of Birth</label>
                <input type="date" name="birthday" required style="background: var(--bg-dark); color: #fff; border: 1px solid var(--border); padding: 8px; border-radius: 6px; width: 100%;">
            </div>

            <div class="input-group">
                <label>Work Team</label>
                <select name="group_id" style="background: var(--bg-dark); color: #fff; border: 1px solid var(--border); padding: 8px; border-radius: 6px; width: 100%;">
                    <option value="">— Assign Later / Independent —</option>
                    <?php foreach ($groups as $g): ?>
                        <option value="<?php echo $g['id']; ?>">
                            <?php echo htmlspecialchars($g['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="input-group">
                <label>Admin Code <span style="color: var(--muted); font-size: 12px;">(leave blank if not an admin)</span></label>
                <input type="password" name="admin_code" placeholder="Optional — admin access code">
            </div>

            <button type="submit" class="btn-login" style="margin-top: 15px;">Register Account</button>
        </form>

        <p style="text-align:center; margin-top:15px; color:var(--muted);">
            Already have an account? <a href="login.php" style="color:var(--primary);">Login here</a>
        </p>
    </div>
</body>
</html>