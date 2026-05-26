/* ===== TASKFLOW — index.js (vue liste, sans login) ===== */

const API = 'api.php';

let tasks = [];
// Utilisateur par défaut : admin (pas de login requis)
let currentUser = { username: 'user', role: 'admin' };

// DOM refs
const toastCont    = document.getElementById('toast-container');
const activeCount  = document.getElementById('active-count');
const urgentCount  = document.getElementById('urgent-count');
const btnAdd       = document.getElementById('btn-add');
const inputTitle   = document.getElementById('task-title');
const inputDesc    = document.getElementById('task-desc');
const inputDue     = document.getElementById('task-due');
const feedback     = document.getElementById('form-feedback');
const listContainer = document.getElementById('task-list-container');

const statusOrder  = ['todo', 'doing', 'done'];
const statusLabels = { todo: 'To Do', doing: 'In Progress', done: 'Done' };

// ─────────────────────────────────────────────────────────────
//  API
// ─────────────────────────────────────────────────────────────
async function apiFetch(method, params = '', body = null) {
  const url  = API + (params ? '?' + params : '');
  const opts = { method, credentials: 'include', headers: { 'Content-Type': 'application/json' } };
  if (body) opts.body = JSON.stringify(body);
  const res  = await fetch(url, opts);
  const data = await res.json();
  if (!res.ok) throw new Error(data.error ?? 'Erreur serveur');
  return data;
}

const apiGet    = () => apiFetch('GET');
const apiPost   = body => apiFetch('POST', '', body);
const apiPut    = (id, body) => apiFetch('PUT', 'id=' + id, body);
const apiDelete = id => apiFetch('DELETE', 'id=' + id);

async function loadTasks() {
  try {
    const data = await apiGet();
    tasks = data.tasks ?? [];
    renderList();
  } catch (err) {
    toast('Erreur chargement : ' + err.message, 'error');
  }
}

// ─────────────────────────────────────────────────────────────
//  RENDER LISTE
// ─────────────────────────────────────────────────────────────
function renderList() {
  if (!listContainer) return;

  if (tasks.length === 0) {
    listContainer.innerHTML = '<div class="empty-list">No tasks yet. Create one →</div>';
  } else {
    listContainer.innerHTML = tasks.map(task => buildListItem(task)).join('');
    tasks.forEach(task => {
      const cardDiv = document.querySelector(`.list-card[data-id="${task.id}"]`);
      if (!cardDiv) return;
      const advanceBtn = cardDiv.querySelector('.action-advance');
      const rewindBtn  = cardDiv.querySelector('.action-rewind');
      const deleteBtn  = cardDiv.querySelector('.action-delete');
      if (advanceBtn) advanceBtn.addEventListener('click', () => changeStatus(task.id, 'advance'));
      if (rewindBtn)  rewindBtn.addEventListener('click',  () => changeStatus(task.id, 'rewind'));
      if (deleteBtn)  deleteBtn.addEventListener('click',  () => deleteTask(task.id));
    });
  }

  const active = tasks.filter(t => t.status !== 'done').length;
  const urgent = tasks.filter(t => t.priority === 'high' && t.status !== 'done').length;
  activeCount.textContent = active;
  urgentCount.textContent = urgent;
}

function buildListItem(task) {
  const idx      = statusOrder.indexOf(task.status);
  const canAdvance = idx < statusOrder.length - 1;
  const canRewind  = idx > 0;
  const isAdmin  = currentUser && currentUser.role === 'admin';
  const due      = task.due_date ?? task.due ?? '';
  const overdue  = due && new Date(due) < new Date(new Date().toDateString()) && task.status !== 'done';

  return `
    <div class="list-card" data-id="${task.id}">
      <div class="list-header">
        <span class="list-title">${escHtml(task.title)}</span>
        <div class="list-actions">
          ${canRewind  ? `<button class="btn-sm action-rewind"  title="Reculer">←</button>` : ''}
          ${canAdvance ? `<button class="btn-sm action-advance" title="Avancer">→</button>` : ''}
          ${isAdmin    ? `<button class="btn-sm btn-danger action-delete" title="Supprimer">✕</button>` : ''}
        </div>
      </div>
      ${task.description ? `<div class="list-desc">${escHtml(task.description)}</div>` : ''}
      <div class="list-meta">
        <span class="badge badge-${task.priority}">${task.priority}</span>
        <span class="status-badge">${statusLabels[task.status] ?? task.status}</span>
        ${due ? `<span class="${overdue ? 'overdue' : ''}" style="font-size:0.7rem;">📅 ${formatDate(due)}${overdue ? ' ⚠' : ''}</span>` : ''}
      </div>
    </div>
  `;
}

function formatDate(iso) {
  if (!iso) return '';
  const [y, m, d] = iso.split('-');
  return `${d}/${m}/${y}`;
}

function escHtml(str = '') {
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}

// ─────────────────────────────────────────────────────────────
//  ACTIONS
// ─────────────────────────────────────────────────────────────
async function changeStatus(taskId, direction) {
  const task = tasks.find(t => t.id === taskId);
  if (!task) return;
  const idx = statusOrder.indexOf(task.status);
  let newStatus = null;
  if (direction === 'advance' && idx < statusOrder.length - 1) newStatus = statusOrder[idx + 1];
  if (direction === 'rewind'  && idx > 0)                      newStatus = statusOrder[idx - 1];
  if (!newStatus) return;

  try {
    const data = await apiPut(taskId, { status: newStatus });
    const index = tasks.findIndex(t => t.id === taskId);
    if (index !== -1) tasks[index] = data.task;
    renderList();
    toast(newStatus === 'done' ? '🎉 Tâche terminée !' : 'Statut mis à jour.', 'success');
  } catch (err) {
    toast(err.message, 'error');
  }
}

async function deleteTask(taskId) {
  if (!confirm('Supprimer cette tâche ?')) return;
  try {
    await apiDelete(taskId);
    tasks = tasks.filter(t => t.id !== taskId);
    renderList();
    toast('Tâche supprimée.', 'info');
  } catch (err) {
    toast(err.message, 'error');
  }
}

// ─────────────────────────────────────────────────────────────
//  CRÉATION TÂCHE
// ─────────────────────────────────────────────────────────────
btnAdd.addEventListener('click', async () => {
  const title = inputTitle.value.trim();
  if (!title) {
    feedback.textContent = 'Le titre est obligatoire.';
    inputTitle.focus();
    return;
  }
  feedback.textContent = '';

  const priority = document.querySelector('input[name="priority"]:checked')?.value ?? 'medium';

  try {
    const data = await apiPost({
      title,
      desc: inputDesc.value.trim(),
      priority,
      due: inputDue.value || ''
    });
    tasks.unshift(data.task);
    renderList();
    toast('Tâche créée !', 'success');
    inputTitle.value = '';
    inputDesc.value  = '';
    inputDue.value   = '';
    document.querySelector('input[name="priority"][value="medium"]').checked = true;
    inputTitle.focus();
  } catch (err) {
    feedback.textContent = err.message;
    toast(err.message, 'error');
  }
});

inputTitle.addEventListener('keydown', e => { if (e.key === 'Enter') btnAdd.click(); });

// ─────────────────────────────────────────────────────────────
//  TOAST
// ─────────────────────────────────────────────────────────────
function toast(msg, type = 'success') {
  const el   = document.createElement('div');
  el.className = `toast toast--${type}`;
  const icon = { success: '✓', error: '✕', info: 'ℹ' }[type] ?? 'ℹ';
  el.innerHTML = `<span>${icon}</span><span>${msg}</span>`;
  toastCont.appendChild(el);
  setTimeout(() => {
    el.classList.add('hiding');
    el.addEventListener('animationend', () => el.remove());
  }, 3000);
}

// ─────────────────────────────────────────────────────────────
//  INIT — chargement direct, pas de login
// ─────────────────────────────────────────────────────────────
loadTasks();