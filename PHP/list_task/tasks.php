<?php

session_start();

require_once("../DATABASE/db.php");

$_SESSION['user_id'] = 1;
?>

<!doctype html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Task List</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700&family=DM+Mono:wght@300;400;500&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="style.css">

</head>

<body>

    <div class="noise"></div>

    <section class="panel">

        <div class="panel-header">

            <h2>Task List</h2>

            <span class="panel-tag">

                TASKS

            </span>
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
            <tbody id="task-table-body">
            </tbody>

        </table>

    </section>

    <div class="toast-container"></div>

    <script>

        async function loadTasks() {

            try {
                const response = await fetch("../DATABASE/api.php",
                    {
                        credentials: "include"
                    }
                );
                const data = await response.json();
                const tbody =
                    document.getElementById(
                        "task-table-body"
                    );
                tbody.innerHTML = "";
                data.tasks.forEach(task => {
                    tbody.innerHTML += `

                    <tr data-id="${task.id}" class="${task.status == "done" ? "completed" : ""}">
                    <td>
                    ${task.title}
                    </td>
                    <td>
                    ${task.description ?? ""}
                    </td>
                    <td>
                    ${task.created_at ?? ""}
                    </td>
                    <td>
                    <span class="badge ${task.priority}">
                    ${task.priority}
                    </span>
                    </td>
                    <td>
                    ${task.status}
                    </td>
                    <td>
                    ${task.due_date ?? ""}
                    </td>
                    <td class="actions">
                    <button class="done">✔</button>
                    <button class="delete">✕</button>
                    </td>
                    </tr>
                    `;
                });
                updateStats(
                    data.tasks
                );
            }

            catch (error) {

                console.log(
                    error
                );

            }

        }
        loadTasks();

    </script>

    <script src="index.js"></script>

</body>

</html>