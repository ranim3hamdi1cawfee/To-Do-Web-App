<?php
session_start();
require_once("db.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if ($row && password_verify($password, $row["password"])) {
        $_SESSION["user_id"] = $row["id"];
        $_SESSION["role"] = $row["role"];
        $_SESSION["group_id"] = $row["group_id"];
        header("Location: ../welcome/welcome.php");
        exit();
    } else {
        $error = "Verify your credentials and try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body class="login-page">
    <div class="container-box">
        <h1 class="title">Login</h1>

        <div id="error-box" class="alert alert-danger" style="display:<?php echo $error ? 'flex' : 'none'; ?>; justify-content:space-between; align-items:center;">
            <span><?php echo htmlspecialchars($error); ?></span>
            <span onclick="document.getElementById('error-box').style.display='none'" style="cursor:pointer; font-size:18px;">&times;</span>
        </div>

        <form action="" method="POST">
            <div class="input-group">
                <label>username</label>
                <input type="text" name="username" placeholder="Enter username" required>
                <small style="color:var(--muted); font-size:12px;">We'll never share your username with anyone else.</small>
            </div>
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn-login">Login</button>
        </form>

        <p style="text-align:center; margin-top:15px; color:var(--muted);">
            No account? <a href="../login_logic/register.php" style="color:var(--primary);">Register here</a>
        </p>
    </div>
</body>
</html>