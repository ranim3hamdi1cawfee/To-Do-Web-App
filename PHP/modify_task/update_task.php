<?php
/* ============================================================
   update_task.php
   Reçoit les données du formulaire (en JSON) et met à jour
   la tâche dans la base.
   ============================================================ */

session_start();
header('Content-Type: application/json');

require_once '../login_logic/db.php';

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Non connecté.']);
    exit;
}

$role = $_SESSION['role'];  // 'Admin' ou 'Regular'

// Lire les données JSON envoyées par modify_task.js
$body = json_decode(file_get_contents('php://input'), true);

if (!$body) {
    echo json_encode(['success' => false, 'error' => 'Données invalides.']);
    exit;
}

// Récupérer l'id de la tâche
$taskId = (int) $body['id'];

if ($taskId <= 0) {
    echo json_encode(['success' => false, 'error' => 'Id de tâche manquant.']);
    exit;
}

// Connexion à la base
$db = getDB();

// Vérifier que la tâche existe
$stmt = $db->prepare('SELECT * FROM task WHERE id = :id');
$stmt->execute([':id' => $taskId]);
$task = $stmt->fetch();

if (!$task) {
    echo json_encode(['success' => false, 'error' => 'Tâche introuvable.']);
    exit;
}

// Traduire le status (formulaire) en movement (base)
$newStatus = $body['status'];
if ($newStatus === 'todo')        $movement = 'andante';
elseif ($newStatus === 'doing')   $movement = 'moderato';
elseif ($newStatus === 'done')    $movement = 'allegro';
else $movement = 'andante';


// CAS 1 : Admin modifie tous les champs
if ($role === 'Admin') {

    $newTitle    = trim($body['title']);
    $newDesc     = trim($body['description']);
    $newPriority = $body['priority'];
    $newDueDate  = $body['due_date'];

    // Vérifier que le titre n'est pas vide
    if ($newTitle === '') {
        echo json_encode(['success' => false, 'error' => 'Le titre est obligatoire.']);
        exit;
    }

    // Transformer la description en JSON pour la colonne tags
    if ($newDesc === '') {
        $tagsJson = json_encode([]);
    } else {
        $tagsJson = json_encode([$newDesc]);
    }

    // Si la date est vide, on met NULL
    if ($newDueDate === '') {
        $newDueDate = null;
    }

    // Faire l'UPDATE
    $sql = 'UPDATE task
            SET title = :title,
                priority = :priority,
                movement = :movement,
                due_date = :due_date,
                tags = :tags
            WHERE id = :id';

    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':title'    => $newTitle,
        ':priority' => $newPriority,
        ':movement' => $movement,
        ':due_date' => $newDueDate,
        ':tags'     => $tagsJson,
        ':id'       => $taskId
    ]);

}
// CAS 2 : User modifie seulement le statut
else {

    $sql = 'UPDATE task SET movement = :movement WHERE id = :id';

    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':movement' => $movement,
        ':id'       => $taskId
    ]);
}

// Tout s'est bien passé : réponse de succès
echo json_encode([
    'success' => true,
    'message' => 'Task updated successfully.'
]);