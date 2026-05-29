<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}
require_once 'db.php';

function respond(int $code, mixed $data): void
{
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function requireLogin(): void
{
    if (!isset($_SESSION['user_id'])) {
        respond(401, ['success' => false, 'error' => 'Non connecté.']);
    }
}

function requireAdmin(): void
{
    requireLogin();
    if ($_SESSION['role'] !== 'Admin') {
        respond(403, ['success' => false, 'error' => 'Action réservée aux administrateurs.']);
    }
}

const STATUS_TO_MOVEMENT = [
    'todo'  => 'andante',
    'doing' => 'moderato',
    'done'  => 'allegro',
];
const MOVEMENT_TO_STATUS = [
    'andante'  => 'todo',
    'moderato' => 'doing',
    'allegro'  => 'done',
];

function normalizeTask(array $row): array
{
    $tags        = $row['tags'] ?? '[]';
    $tagsDecoded = json_decode($tags, true);
    $description = is_array($tagsDecoded) ? implode(', ', $tagsDecoded) : ($tags ?? '');
    return [
        'id'          => (string) $row['id'],
        'title'       => $row['title'],
        'description' => $description,
        'priority'    => $row['priority'] ?? 'medium',
        'status'      => MOVEMENT_TO_STATUS[$row['movement']] ?? 'todo',
        'due_date'    => $row['due_date'] ?? null,
        'created_by'  => 'system',
    ];
}

$method = $_SERVER['REQUEST_METHOD'];
$id     = $_GET['id'] ?? null;
$body   = json_decode(file_get_contents('php://input'), true) ?? [];

switch ($method) {

    case 'GET':
        requireLogin();
        $db  = getDB();

        // ── CHANGEMENT : filtre par user_id connecté ──────────────────────
        // Chaque user ne voit QUE ses propres tâches
        // $_SESSION['user_id'] est défini lors du login
        $sql    = 'SELECT * FROM task WHERE user_id = :user_id';
        $params = [':user_id' => $_SESSION['user_id']];

        if (!empty($_GET['group'])) {
            $sql .= ' AND group_id = :group';
            $params[':group'] = $_GET['group'];
        }
        if (!empty($_GET['status'])) {
            $movement = STATUS_TO_MOVEMENT[$_GET['status']] ?? $_GET['status'];
            $sql .= ' AND movement = :movement';
            $params[':movement'] = $movement;
        }
        $sql .= ' ORDER BY id DESC';
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $tasks = array_map('normalizeTask', $stmt->fetchAll());
        respond(200, ['success' => true, 'tasks' => $tasks]);

    case 'POST':
        requireLogin();
        if (empty($body['title']))
            respond(422, ['success' => false, 'error' => 'Titre obligatoire.']);

        $db   = getDB();

        // ── CHANGEMENT : on insère aussi user_id dans la tâche ────────────
        // La tâche est liée à l'utilisateur qui la crée
        $stmt = $db->prepare(
            'INSERT INTO task (title, priority, movement, due_date, tags, user_id)
             VALUES (:title, :priority, :movement, :due, :tags, :user_id)'
        );
        $stmt->execute([
            ':title'    => trim($body['title']),
            ':priority' => $body['priority'] ?? 'medium',
            ':movement' => 'andante',
            ':due'      => !empty($body['due']) ? $body['due'] : null,
            ':tags'     => json_encode($body['desc'] ? [$body['desc']] : []),
            ':user_id'  => $_SESSION['user_id'], // ← lie la tâche à l'user connecté
        ]);

        $newId = $db->lastInsertId();
        $s = $db->prepare('SELECT * FROM task WHERE id = :id');
        $s->execute([':id' => $newId]);
        respond(201, ['success' => true, 'task' => normalizeTask($s->fetch())]);

    case 'PUT':
        requireAdmin();
        if (!$id)
            respond(400, ['success' => false, 'error' => 'id manquant.']);

        $db     = getDB();
        $sets   = [];
        $params = [':id' => $id];

        if (array_key_exists('title', $body)) {
            $sets[] = 'title = :title';
            $params[':title'] = trim($body['title']);
        }
        if (array_key_exists('priority', $body)) {
            $sets[] = 'priority = :priority';
            $params[':priority'] = $body['priority'];
        }
        if (array_key_exists('status', $body)) {
            $sets[] = 'movement = :movement';
            $params[':movement'] = STATUS_TO_MOVEMENT[$body['status']] ?? 'andante';
        }
        if (array_key_exists('due', $body)) {
            $sets[] = 'due_date = :due_date';
            $params[':due_date'] = $body['due'] === '' ? null : $body['due'];
        }

        if (empty($sets))
            respond(400, ['success' => false, 'error' => 'Rien à modifier.']);

        $db->prepare('UPDATE task SET ' . implode(', ', $sets) . ' WHERE id = :id')->execute($params);
        $s = $db->prepare('SELECT * FROM task WHERE id = :id');
        $s->execute([':id' => $id]);
        respond(200, ['success' => true, 'task' => normalizeTask($s->fetch())]);

    case 'DELETE':
        requireAdmin();
        if (!$id)
            respond(400, ['success' => false, 'error' => 'id manquant.']);

        $db   = getDB();
        $stmt = $db->prepare('DELETE FROM task WHERE id = :id');
        $stmt->execute([':id' => $id]);

        if ($stmt->rowCount() === 0)
            respond(404, ['success' => false, 'error' => 'Introuvable.']);

        respond(200, ['success' => true]);

    default:
        respond(405, ['success' => false, 'error' => 'Méthode non autorisée.']);
}