<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Credentials: true');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
require_once 'db.php';
$body = json_decode(file_get_contents('php://input'), true) ?? [];
if (empty($body['username']) || empty($body['password'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Identifiants manquants.']);
    exit;
}
$db   = getDB();
$stmt = $db->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
$stmt->execute([':username' => trim($body['username'])]);
$user = $stmt->fetch();
if ($user && password_verify($body['password'], $user['password'])) {
    session_regenerate_id(true);
    $_SESSION['user_id']  = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role']     = $user['role'];
    echo json_encode(['success' => true, 'username' => $user['username'], 'role' => $user['role']]);
} else {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Identifiants incorrects.']);
}