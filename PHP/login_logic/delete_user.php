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

    $stmt = $conn->prepare("DELETE FROM user WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    // If user deleted themselves, log them out
    if ((int)$_SESSION['user_id'] === $id) {
        session_destroy();
    }
}

$conn->close();
header("Location: ../login_logic/login.php");
exit();