<?php
session_start();

// Redirection si non connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_logic/login.php");
    exit();
}

// On récupère l'utilisateur courant pour l'utiliser éventuellement dans le JS
$currentUser = [
    'username' => $_SESSION['username'],
    'role'     => $_SESSION['role']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planning Mode</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Mono:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        header {
            position: sticky;
            top: var(--navbar-height, 60px);
            z-index: 99;
        }
        nav {
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .task-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        /* ... le reste de votre CSS ... */
    </style>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
    <?php require_once("../welcome/navbar.php"); ?>
    <div class="noise"></div>


    <header>
    <div class="header-left">
        <span class="page-title">✎ Formulaire</span>
    </div>
    <div class="header-right">
        <div class="stat-pill">
            <span class="stat-num" id="active-count">0</span>
            <span class="stat-label">Active</span>
        </div>
        <div class="stat-pill stat-pill--accent">
            <span class="stat-num" id="urgent-count">0</span>
            <span class="stat-label">Urgent</span>
        </div>
    </div>
</header>
    <main>
        <aside class="panel panel--create">
            <div class="panel-header">
                <h2>New Task</h2>
                <span class="panel-tag">+CREATE</span>
            </div>
            <!-- Formulaire de création (inchangé) -->
            <div class="form-group">
                <label for="task-title">Task Title</label>
                <input type="text" id="task-title" placeholder="What needs to be done?" autocomplete="off">
            </div>
            <div class="form-group">
                <label for="task-desc">Details</label>
                <textarea id="task-desc" placeholder="Describe the task..." rows="4"></textarea>
            </div>
            <div class="form-group">
                <label>Priority Level</label>
                <div class="priority-grid">
                    <label class="priority-option priority-option--low">
                        <input type="radio" name="priority" value="low">
                        <span class="priority-card"><span class="priority-icon">▽</span><span>Low</span></span>
                    </label>
                    <label class="priority-option priority-option--med">
                        <input type="radio" name="priority" value="medium" checked>
                        <span class="priority-card"><span class="priority-icon">◇</span><span>Medium</span></span>
                    </label>
                    <label class="priority-option priority-option--high">
                        <input type="radio" name="priority" value="high">
                        <span class="priority-card"><span class="priority-icon">△</span><span>High</span></span>
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label for="task-due">Due Date</label>
                <input type="date" id="task-due">
            </div>
            <button class="btn-add" id="btn-add">
                <span class="btn-text">Add Task</span>
                <span class="btn-icon">→</span>
            </button>
            <div class="form-feedback" id="form-feedback"></div>
        </aside>

        <section class="panel panel--list">
            <div class="panel-header">
                <h2>All Tasks</h2>
                <span class="panel-tag">LIST</span>
            </div>
            <div id="task-list-container" class="task-list">
                <div class="empty-list">No tasks yet. Create one →</div>
            </div>
        </section>
    </main>

    <div class="toast-container" id="toast-container"></div>

    <!-- On passe l'utilisateur courant au JS via data ou variable globale -->
    <script>
        window.currentUser = <?php echo json_encode($currentUser); ?>;
    </script>
    <script src="index.js"></script>
</body>
</html>