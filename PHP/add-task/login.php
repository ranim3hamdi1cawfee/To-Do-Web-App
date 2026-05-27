<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Credentials: true');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

$body = json_decode(file_get_contents('php://input'), true) ?? [];
if (empty($body['username']) || empty($body['password'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Identifiants manquants.']);
    exit;
}

$conn = new mysqli('127.0.0.1', 'root', '', 'taskflow', 3307);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Connexion DB échouée.']);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM user WHERE username = ? LIMIT 1");
$stmt->bind_param("s", $body['username']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($user && password_verify($body['password'], $user['password'])) {
    session_regenerate_id(true);
    $_SESSION['user_id']  = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role']     = $user['role'];
    $_SESSION['group_id'] = $user['group_id'] ?? null;
    echo json_encode(['success' => true, 'username' => $user['username'], 'role' => $user['role']]);
} else {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Identifiants incorrects.']);
}
$stmt->close();
$conn->close();