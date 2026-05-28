/* ===== TASKFLOW — index.js  ===== */

const API        = 'api.php';
const LOGOUT_URL = '../login_logic/logout.php'; 

let tasks       = [];
let currentUser = window.currentUser || null;
let filterPriority = 'all';

// DOM refs
const toastCont     = document.getElementById('toast-container');
const activeCount   = document.getElementById('active-count');
const urgentCount   = document.getElementById('urgent-count');
const btnAdd        = document.getElementById('btn-add');
const inputTitle    = document.getElementById('task-title');
const inputDesc     = document.getElementById('task-desc');
const inputDue      = document.getElementById('task-due');
const feedback      = document.getElementById('form-feedback');
const listContainer = document.getElementById('task-list-container');

const statusOrder  = ['todo', 'doing', 'done'];
const statusLabels = { todo: 'To Do', doing: 'In Progress', done: 'Done' };

function isAdmin() { return currentUser && currentUser.role === 'Admin'; }

// ─────────────────────────────────────────────────────────────
//  API (identique)
// ─────────────────────────────────────────────────────────────
async function apiFetch(method, params = '', body = null) {
    const url  = API + (params ? '?' + params : '');
    const opts = { method, credentials:'include', headers:{'Content-Type':'application/json'} };
    if (body) opts.body = JSON.stringify(body);
    const res  = await fetch(url, opts);
    const data = await res.json();
    if (res.status === 401) {
        // Session expirée → redirection vers login
        window.location.href = '../login_logic/login.php';
        throw new Error('Session expirée.');
    }
    if (!res.ok) throw new Error(data.error ?? 'Erreur serveur');
    return data;
}

const apiGet    = () => apiFetch('GET');
const apiPost   = body => apiFetch('POST', '', body);
const apiPut    = (id, body) => apiFetch('PUT', 'id=' + id, body);
const apiDelete = id => apiFetch('DELETE', 'id=' + id);

// ─────────────────────────────────────────────────────────────
//  Chargement & rendu
// ─────────────────────────────────────────────────────────────
async function loadTasks() {
    try {
        const data = await apiGet();
        tasks = data.tasks ?? [];
        addFilterBar();
        renderList();
    } catch (err) {
        toast('Erreur chargement : ' + err.message, 'error');
    }
}

function addFilterBar() {
    if (document.getElementById('filter-bar')) return;
    const panel = document.querySelector('.panel--list');
    const header = panel.querySelector('.panel-header');
    const bar = document.createElement('div');
    bar.id = 'filter-bar';
    bar.style.cssText = 'display:flex;gap:0.5rem;margin-bottom:1rem;flex-wrap:wrap;';
    bar.innerHTML = `
        <button class="filter-btn filter-active" data-val="all">All</button>
        <button class="filter-btn filter-low"    data-val="low">▽ Low</button>
        <button class="filter-btn filter-medium" data-val="medium">◇ Medium</button>
        <button class="filter-btn filter-high"   data-val="high">△ High</button>
    `;
    const style = document.createElement('style');
    style.textContent = `
        .filter-btn{background:var(--surface2);border:1px solid var(--border);border-radius:99px;padding:0.25rem 0.75rem;font-family:var(--font-mono);font-size:11px;cursor:pointer;color:var(--muted);transition:all 0.2s;letter-spacing:0.5px;}
        .filter-btn:hover{border-color:var(--muted);color:var(--text);}
        .filter-btn.filter-active{background:var(--surface);border-color:var(--text);color:var(--text);}
        .filter-low.filter-active{border-color:var(--low);color:var(--low);}
        .filter-medium.filter-active{border-color:var(--medium);color:var(--medium);}
        .filter-high.filter-active{border-color:var(--high);color:var(--high);}
    `;
    document.head.appendChild(style);
    header.insertAdjacentElement('afterend', bar);

    bar.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            filterPriority = btn.dataset.val;
            bar.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('filter-active'));
            btn.classList.add('filter-active');
            renderList();
        });
    });
}

function renderList() {
    if (!listContainer) return;
    const filtered = filterPriority === 'all' ? tasks : tasks.filter(t => t.priority === filterPriority);
    if (filtered.length === 0) {
        listContainer.innerHTML = '<div class="empty-list">No tasks found.</div>';
    } else {
        const groups = { todo: [], doing: [], done: [] };
        filtered.forEach(t => { if (groups[t.status]) groups[t.status].push(t); else groups['todo'].push(t); });
        listContainer.innerHTML = statusOrder.map(status => {
            const count = groups[status].length;
            return `
                <div class="status-group">
                    <div class="status-group-header">
                        <span class="status-group-title">${statusLabels[status]}</span>
                        <span class="status-group-count">${count}</span>
                    </div>
                    <div class="status-group-body" data-status="${status}">
                        ${count === 0
                            ? `<div class="empty-list" style="padding:1rem;">No tasks</div>`
                            : groups[status].map(task => buildListItem(task)).join('')
                        }
                    </div>
                </div>`;
        }).join('');
        // Attach events
        filtered.forEach(task => {
            const cardDiv = document.querySelector(`.list-card[data-id="${task.id}"]`);
            if (!cardDiv) return;
            const advanceBtn = cardDiv.querySelector('.action-advance');
            const rewindBtn  = cardDiv.querySelector('.action-rewind');
            const deleteBtn  = cardDiv.querySelector('.action-delete');
            if (advanceBtn) advanceBtn.addEventListener('click', () => changeStatus(task.id, 'advance'));
            if (rewindBtn)  rewindBtn.addEventListener('click',  () => changeStatus(task.id, 'rewind'));
            if (deleteBtn)  deleteBtn.addEventListener('click',  () => deleteTask(task.id));
        });
        if (isAdmin()) initDragDrop();
    }
    const active = tasks.filter(t => t.status !== 'done').length;
    const urgent = tasks.filter(t => t.priority === 'high' && t.status !== 'done').length;
    activeCount.textContent = active;
    urgentCount.textContent = urgent;
}

// Drag & drop (identique)
let draggedId = null;
function initDragDrop() {
    document.querySelectorAll('.list-card').forEach(card => {
        card.setAttribute('draggable', 'true');
        card.addEventListener('dragstart', e => {
            draggedId = card.dataset.id;
            card.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
        });
        card.addEventListener('dragend', () => {
            card.classList.remove('dragging');
            document.querySelectorAll('.status-group-body').forEach(z => z.classList.remove('drag-over'));
        });
    });
    document.querySelectorAll('.status-group-body').forEach(zone => {
        zone.addEventListener('dragover', e => {
            e.preventDefault();
            zone.classList.add('drag-over');
        });
        zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
        zone.addEventListener('drop', async e => {
            e.preventDefault();
            zone.classList.remove('drag-over');
            const newStatus = zone.dataset.status;
            const task = tasks.find(t => t.id === draggedId);
            if (!task || task.status === newStatus) return;
            try {
                const data = await apiPut(draggedId, { status: newStatus });
                const index = tasks.findIndex(t => t.id === draggedId);
                if (index !== -1) tasks[index] = data.task;
                renderList();
                toast(newStatus === 'done' ? '🎉 Tâche terminée !' : 'Statut mis à jour.', 'success');
            } catch (err) { toast(err.message, 'error'); }
        });
    });
}

function buildListItem(task) {
    const idx        = statusOrder.indexOf(task.status);
    const canAdvance = idx < statusOrder.length - 1;
    const canRewind  = idx > 0;
    const due        = task.due_date ?? '';
    const overdue    = due && new Date(due) < new Date(new Date().toDateString()) && task.status !== 'done';
    return `
        <div class="list-card priority-border-${task.priority}" data-id="${task.id}" data-priority="${task.priority}">
            <div class="list-header">
                <span class="list-title">${escHtml(task.title)}</span>
                <div class="list-actions">
                    <a class="btn-sm btn-edit" href="../modify_task/modify_task.php?id=${task.id}" title="Modifier">MODIFY✎</a>                
                    ${isAdmin() && canRewind  ? `<button class="btn-sm action-rewind"  title="Reculer">←</button>` : ''}
                    ${isAdmin() && canAdvance ? `<button class="btn-sm action-advance" title="Avancer">→</button>` : ''}
                    ${isAdmin()               ? `<button class="btn-sm btn-danger action-delete" title="Supprimer">✕</button>` : ''}
                </div>
            </div>
            ${task.description ? `<div class="list-desc">${escHtml(task.description)}</div>` : ''}
            <div class="list-meta">
                <span class="badge badge-${task.priority}">${task.priority}</span>
                ${due ? `<span class="${overdue ? 'overdue' : ''}" style="font-size:0.7rem;">📅 ${formatDate(due)}${overdue ? ' ⚠' : ''}</span>` : ''}
            </div>
        </div>`;
}

function formatDate(iso) {
    if (!iso) return '';
    const [y, m, d] = iso.split('-');
    return `${d}/${m}/${y}`;
}
function escHtml(str = '') {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

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
    } catch (err) { toast(err.message, 'error'); }
}

async function deleteTask(taskId) {
    if (!confirm('Supprimer cette tâche ?')) return;
    try {
        await apiDelete(taskId);
        tasks = tasks.filter(t => t.id !== taskId);
        renderList();
        toast('Tâche supprimée.', 'info');
    } catch (err) { toast(err.message, 'error'); }
}

// Création
btnAdd.addEventListener('click', async () => {
    const title = inputTitle.value.trim();
    if (!title) { feedback.textContent = 'Le titre est obligatoire.'; inputTitle.focus(); return; }
    feedback.textContent = '';
    const priority = document.querySelector('input[name="priority"]:checked')?.value ?? 'medium';
    try {
        const data = await apiPost({ title, desc: inputDesc.value.trim(), priority, due: inputDue.value || '' });
        tasks.unshift(data.task);
        renderList();
        toast('Tâche créée !', 'success');
        inputTitle.value = ''; inputDesc.value = ''; inputDue.value = '';
        document.querySelector('input[name="priority"][value="medium"]').checked = true;
        inputTitle.focus();
    } catch (err) { feedback.textContent = err.message; toast(err.message, 'error'); }
});
inputTitle.addEventListener('keydown', e => { if (e.key === 'Enter') btnAdd.click(); });

// Toast
function toast(msg, type = 'success') {
    const el = document.createElement('div');
    el.className = `toast toast--${type}`;
    const icon = { success: '✓', error: '✕', info: 'ℹ' }[type] ?? 'ℹ';
    el.innerHTML = `<span>${icon}</span><span>${msg}</span>`;
    toastCont.appendChild(el);
    setTimeout(() => { el.classList.add('hiding'); el.addEventListener('animationend', () => el.remove()); }, 3000);
}
// Initialisation
async function init() {
    await loadTasks();
}
init();