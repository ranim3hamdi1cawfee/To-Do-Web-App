<?php

function requireLogin(): void {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login_logic/login.php");
        exit();
    }
}

function requireAdmin(): void {
    requireLogin();
    if ($_SESSION['role'] !== 'Admin') {
        http_response_code(403);
        die("Access Denied. Admins only.");
    }
}

function isAdmin(): bool {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'Admin';
}

function isSameUserOrAdmin(int $targetId): bool {
    return isAdmin() || (int)$_SESSION['user_id'] === $targetId;
}