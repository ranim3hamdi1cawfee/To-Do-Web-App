function showToast(message, type) {
  const container = document.querySelector(".toast-container");

  const toast = document.createElement("div");

  toast.className = `toast ${type}`;

  toast.innerHTML = message;

  container.appendChild(toast);

  setTimeout(() => {
    toast.style.animation = "slideOut .4s forwards";

    setTimeout(() => {
      toast.remove();
    }, 400);
  }, 2500);
}

function updateStats() {
  const activeTasks =
    document.querySelectorAll("tbody tr").length -
    document.querySelectorAll("tbody tr.completed").length;

  const urgentTasks = document.querySelectorAll(
    "tbody tr:not(.completed) .badge.high",
  ).length;

  const stats = document.querySelectorAll(".stat-num");

  stats[0].textContent = activeTasks;

  stats[1].textContent = urgentTasks;
}

updateStats();
const API = "../DATABASE/api.php";

async function apiPut(id, body) {
  const response = await fetch(API + "?id=" + id, {
    method: "PUT",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(body),
  });

  return await response.json();
}

async function apiDelete(id) {
  const response = await fetch(API + "?id=" + id, {
    method: "DELETE",
  });

  return await response.json();
}

document.addEventListener("click", async (e) => {
  if (e.target.classList.contains("done")) {
    const row = e.target.closest("tr");

    const id = row.dataset.id;

    const currentStatus = row.children[4].textContent.trim();

    const newStatus = currentStatus == "done" ? "todo" : "done";

    await apiPut(id, {
      status: newStatus,
    });

    loadTasks();
    showToast(
      newStatus == "done" ? "✓ Task completed" : "↺ Task restored",

      newStatus == "done" ? "toast-success" : "toast-edit",
    );
  }

  if (e.target.classList.contains("delete")) {
    const row = e.target.closest("tr");

    const id = row.dataset.id;

    if (!confirm("Delete this task ?")) return;

    await apiDelete(id);

    loadTasks();
  }
});
