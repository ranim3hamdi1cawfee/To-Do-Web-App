<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_logic/login.php");
    exit();
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task List</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700&family=DM+Mono:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php require_once("../welcome/navbar.php"); ?>
    <div class="noise"></div>
    <section class="panel">
        <div class="panel-header">
            <h2>Task List</h2>
            <span class="panel-tag">TASKS</span>
        </div>

        <div class="top-stats">
            <div class="stat-pill">
                <span class="stat-num" id="active-count">0</span>
                <span class="stat-label">Active</span>
            </div>
            <div class="stat-pill urgent">
                <span class="stat-num" id="urgent-count">0</span>
                <span class="stat-label">Urgent</span>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Task</th>
                    <th>Description</th>
                    <th>Start Date</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Deadline</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="task-table-body"></tbody>
        </table>
    </section>

    <div class="toast-container"></div>

    <script>
        async function loadTasks() {
            try {
                const response = await fetch("../add-task/api.php", {
                    credentials: "include"
                });
                const data = await response.json();
                const tbody = document.getElementById("task-table-body");
                tbody.innerHTML = "";

                if (!data.tasks || data.tasks.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="7" style="text-align:center; color: var(--muted);">No tasks yet.</td></tr>`;
                    return;
                }

                data.tasks.forEach(task => {
                    tbody.innerHTML += `
                        <tr data-id="${task.id}" class="${task.status === 'done' ? 'completed' : ''}">
                            <td>${task.title}</td>
                            <td>${task.description ?? ''}</td>
                            <td>${task.created_at ?? ''}</td>
                            <td><span class="badge ${task.priority}">${task.priority}</span></td>
                            <td>${task.status}</td>
                            <td>${task.due_date ?? ''}</td>
                            <td class="actions">
                                <button class="done" onclick="markDone('${task.id}')">✔</button>
                                <button class="delete" onclick="deleteTask('${task.id}')">✕</button>
                            </td>
                        </tr>
                    `;
                });

                updateStats(data.tasks);

            } catch (error) {
                console.error("Failed to load tasks:", error);
            }
        }

        function updateStats(tasks) {
            const active = tasks.filter(t => t.status !== 'done').length;
            const urgent = tasks.filter(t => t.priority === 'high' && t.status !== 'done').length;
            document.getElementById('active-count').textContent = active;
            document.getElementById('urgent-count').textContent = urgent;
        }

        loadTasks();
    </script>

    <script src="index.js"></script>
</body>

</html>