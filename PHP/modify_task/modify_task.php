<?php

session_start();
<<<<<<< Updated upstream
require_once __DIR__ . '/../add-task/db.php';
require_once __DIR__ . '/../login_logic/auth_guard.php';
=======

require_once '../add-task/db.php';
require_once '../login_logic/auth_guard.php';

>>>>>>> Stashed changes
// Vérifier que l'utilisateur est connecté
requireLogin();

// Récupérer les infos de la session
$role     = $_SESSION['role'];        // 'Admin' ou 'Regular'
$username = $_SESSION['username'];
$isAdmin  = ($role === 'Admin');

// Récupérer l'id de la tâche depuis l'URL
$taskId = (int) $_GET['id'];

if ($taskId <= 0) {
    die("Error: no task ID provided.");
}

// Charger la tâche depuis la base
$db = getDB();
$stmt = $db->prepare('SELECT * FROM task WHERE id = :id');
$stmt->execute([':id' => $taskId]);
$task = $stmt->fetch();

if (!$task) {
    die("Error: task not found.");
}
// Traduire les valeurs de la base vers le format du formulaire :
// movement → status
if ($task['movement'] === 'andante')  $status = 'todo';
elseif ($task['movement'] === 'moderato') $status = 'doing';
elseif ($task['movement'] === 'allegro')  $status = 'done';
else $status = 'todo';

// tags (JSON) → description (texte lisible)
$tagsArray = json_decode($task['tags'], true);
if (!is_array($tagsArray)) {
    $tagsArray = [];
}
$description = implode(', ', $tagsArray);

// Variables prêtes pour le HTML
$title    = $task['title'];
$priority = $task['priority'];
$dueDate  = $task['due_date'];

// Pour bloquer les champs si User
$readonly = $isAdmin ? '' : 'readonly';
$disabled = $isAdmin ? '' : 'disabled';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modify Task</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Mono:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="modify_task.css">
</head>
<body>

<header>
    <div class="header-left">
        <div class="logo-mark">TF</div>
        <div>
            <h1 id="TaskFlow">TaskFlow</h1>
            <span class="mode-badge">✎ Modify Task</span>
        </div>
    </div>
    <div class="header-right">
        <span class="mt-user-badge">
            <?php echo $isAdmin ? 'Admin' : 'User'; ?> — <?php echo htmlspecialchars($username); ?>
        </span>
        <a href="../add-task/index.php" class="mt-back-btn">← Back</a>
        <a href="../login_logic/logout.php" class="mt-back-btn" style="border-color:#ff5f5f; color:#ff5f5f;">Logout</a>
    </div>
</header>


<main class="mt-main">

    <section class="mt-card">

        <h2 class="mt-title">Edit Task #<?php echo $taskId; ?></h2>

        <p>
            <?php if ($isAdmin) { ?>
                <span class="mt-badge mt-badge--admin">ADMIN — tous les champs modifiables</span>
            <?php } else { ?>
                <span class="mt-badge mt-badge--user">USER — statut uniquement</span>
            <?php } ?>
        </p>

        <!-- Zone pour afficher succès / erreur -->
        <div id="mt-feedback" class="mt-feedback" style="display:none"></div>

        <div class="mt-form" id="mt-form">

            <!-- TITLE -->
            <div class="mt-field <?php if (!$isAdmin) echo 'mt-field--locked'; ?>">
                <label>Title</label>
                <input type="text" id="mt-title" value="<?php echo htmlspecialchars($title); ?>" <?php echo $readonly; ?>>
            </div>

            <!-- DESCRIPTION -->
            <div class="mt-field <?php if (!$isAdmin) echo 'mt-field--locked'; ?>">
                <label>Description</label>
                <textarea id="mt-desc" rows="4" <?php echo $readonly; ?>><?php echo htmlspecialchars($description); ?></textarea>
            </div>

            <div class="mt-row">

                <!-- PRIORITY -->
                <div class="mt-field <?php if (!$isAdmin) echo 'mt-field--locked'; ?>">
                    <label>Priority</label>
                    <div class="mt-priority-grid">
                        <label class="mt-priority-opt mt-priority-opt--low">
                            <input type="radio" name="priority" value="low" <?php if ($priority === 'low') echo 'checked'; ?> <?php echo $disabled; ?>>
                            <span class="mt-priority-card">Low</span>
                        </label>
                        <label class="mt-priority-opt mt-priority-opt--medium">
                            <input type="radio" name="priority" value="medium" <?php if ($priority === 'medium') echo 'checked'; ?> <?php echo $disabled; ?>>
                            <span class="mt-priority-card">Medium</span>
                        </label>
                        <label class="mt-priority-opt mt-priority-opt--high">
                            <input type="radio" name="priority" value="high" <?php if ($priority === 'high') echo 'checked'; ?> <?php echo $disabled; ?>>
                            <span class="mt-priority-card">High</span>
                        </label>
                    </div>
                </div>

                <!-- DUE DATE -->
                <div class="mt-field <?php if (!$isAdmin) echo 'mt-field--locked'; ?>">
                    <label>Due Date</label>
                    <input type="date" id="mt-due" value="<?php echo htmlspecialchars($dueDate); ?>" <?php echo $readonly; ?>>
                </div>
            </div>

            <!-- STATUS (toujours modifiable) -->
            <div class="mt-field">
                <label>Status</label>
                <div class="mt-status-grid">
                    <label class="mt-status-opt mt-status-opt--todo">
                        <input type="radio" name="status" value="todo" <?php if ($status === 'todo') echo 'checked'; ?>>
                        <span class="mt-status-card">To Do</span>
                    </label>
                    <label class="mt-status-opt mt-status-opt--doing">
                        <input type="radio" name="status" value="doing" <?php if ($status === 'doing') echo 'checked'; ?>>
                        <span class="mt-status-card">In Progress</span>
                    </label>
                    <label class="mt-status-opt mt-status-opt--done">
                        <input type="radio" name="status" value="done" <?php if ($status === 'done') echo 'checked'; ?>>
                        <span class="mt-status-card">Done</span>
                    </label>
                </div>
            </div>

            <!-- BOUTONS -->
            <div class="mt-actions">
                <a href="../add-task/index.php" class="mt-btn mt-btn--cancel">✕ Cancel</a>
                <button type="button" class="mt-btn mt-btn--save" id="mt-save-btn">
                    <span class="mt-btn-text">Save</span>
                    <span class="mt-btn-icon">→</span>
                </button>
            </div>

        </div>
    </section>
</main>
<!-- Passer les infos PHP au JavaScript -->
<script>
    window.MT_ROLE = "<?php echo $role; ?>";
    window.MT_TASK_ID = <?php echo $taskId; ?>;
</script>
<script src="modify_task.js"></script>
</body>
</html>
