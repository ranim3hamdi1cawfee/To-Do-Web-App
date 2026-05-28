/* ===== TASKFLOW — index.js ===== */

const API        = 'api.php';
const LOGIN_URL  = 'login.php';
const LOGOUT_URL = 'logout.php';

let tasks       = [];
let currentUser = null;
let filterPriority = 'all'; // 'all' | 'low' | 'medium' | 'high'

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
//  LOGIN SCREEN
// ─────────────────────────────────────────────────────────────
function showLoginScreen() {
    document.querySelector('main').style.display   = 'none';
    document.querySelector('header').style.display = 'none';
    if (document.getElementById('login-screen')) return;
    const screen = document.createElement('div');
    screen.id = 'login-screen';
    screen.innerHTML = `
        <div class="login-box">
            <div class="login-logo">TF</div>
            <h1 class="login-title">TaskFlow</h1>
            <p class="login-sub">Connecte-toi pour continuer</p>
            <div class="login-field"><label>Nom d'utilisateur</label><input id="l-user" type="text" placeholder="username" autocomplete="username"></div>
            <div class="login-field"><label>Mot de passe</label><input id="l-pass" type="password" placeholder="••••••••" autocomplete="current-password"></div>
            <div id="l-error" class="login-error"></div>
            <button id="l-btn">Connexion →</button>
        </div>`;
    const style = document.createElement('style');
    style.textContent = `
        #login-screen{position:fixed;inset:0;background:var(--bg);display:flex;align-items:center;justify-content:center;z-index:500;font-family:var(--font-mono);}
        .login-box{background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:2.5rem 2rem;width:100%;max-width:360px;display:flex;flex-direction:column;gap:1.1rem;box-shadow:0 24px 64px rgba(0,0,0,0.6);animation:fadeUp .4s ease both;}
        .login-logo{width:48px;height:48px;background:var(--accent);color:#000;font-family:var(--font-head);font-weight:800;font-size:15px;display:grid;place-items:center;border-radius:10px;}
        .login-title{font-family:var(--font-head);font-weight:800;font-size:1.6rem;letter-spacing:-1px;color:var(--text);}
        .login-sub{font-size:13px;color:var(--muted);margin-top:-.5rem;}
        .login-field{display:flex;flex-direction:column;gap:.4rem;}
        .login-field label{font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--muted);}
        .login-field input{background:var(--surface2);border:1px solid var(--border);border-radius:10px;color:var(--text);font-family:var(--font-mono);font-size:13px;padding:.7rem .9rem;outline:none;}
        .login-field input:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(200,251,75,.1);}
        .login-error{font-size:12px;color:var(--high);min-height:16px;text-align:center;}
        #l-btn{background:var(--accent);color:#000;border:none;border-radius:10px;font-family:var(--font-head);font-weight:700;padding:.85rem;cursor:pointer;transition:.2s;}
        #l-btn:hover{background:#d4ff5a;transform:translateY(-2px);}
        #l-btn:disabled{opacity:.5;cursor:not-allowed;}
    `;
    document.head.appendChild(style);
    document.body.appendChild(screen);
    document.getElementById('l-btn').addEventListener('click', doLogin);
    document.getElementById('l-pass').addEventListener('keydown', e => { if (e.key === 'Enter') doLogin(); });
    document.getElementById('l-user').focus();
}

function hideLoginScreen() {
    const screen = document.getElementById('login-screen');
    if (screen) screen.remove();
    document.querySelector('main').style.display   = '';
    document.querySelector('header').style.display = '';
}

async function doLogin() {
    const username = document.getElementById('l-user').value.trim();
    const password = document.getElementById('l-pass').value;
    const errEl    = document.getElementById('l-error');
    const btn      = document.getElementById('l-btn');
    errEl.textContent = '';
    if (!username || !password) { errEl.textContent = 'Remplis les deux champs.'; return; }
    btn.disabled = true; btn.textContent = 'Connexion…';
    try {
        const res  = await fetch(LOGIN_URL, { method:'POST', credentials:'include', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ username, password }) });
        const data = await res.json();
        if (data.success) {
            currentUser = { username: data.username, role: data.role };
            hideLoginScreen();
            addLogoutButton();
            await loadTasks();
            toast(`Bienvenue, ${data.username} !`, 'success');
        } else {
            errEl.textContent = data.error ?? 'Identifiants incorrects.';
            btn.disabled = false; btn.textContent = 'Connexion →';
        }
    } catch {
        errEl.textContent = 'Impossible de joindre le serveur.';
        btn.disabled = false; btn.textContent = 'Connexion →';
    }
}

function addLogoutButton() {
    const headerRight = document.querySelector('.header-right');
    if (!headerRight || document.getElementById('btn-logout')) return;
    const btn = document.createElement('button');
    btn.id = 'btn-logout';
    btn.textContent = 'Déconnexion';
    btn.style.cssText = 'background:none;border:1px solid var(--border);border-radius:99px;padding:0.3rem 0.85rem;color:var(--muted);font-family:var(--font-mono);font-size:11px;cursor:pointer;transition:0.2s;';
    btn.addEventListener('mouseenter', () => { btn.style.borderColor = 'var(--text)'; btn.style.color = 'var(--text)'; });
    btn.addEventListener('mouseleave', () => { btn.style.borderColor = 'var(--border)'; btn.style.color = 'var(--muted)'; });
    btn.addEventListener('click', async () => {
        await fetch(LOGOUT_URL, { credentials: 'include' });
        currentUser = null; tasks = [];
        btn.remove();
        const filterBar = document.getElementById('filter-bar');
        if (filterBar) filterBar.remove();
        showLoginScreen();
    });
    headerRight.appendChild(btn);
}

// ─────────────────────────────────────────────────────────────
//  FILTER BAR
// ─────────────────────────────────────────────────────────────
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

// ─────────────────────────────────────────────────────────────
//  API
// ─────────────────────────────────────────────────────────────
async function apiFetch(method, params = '', body = null) {
    const url  = API + (params ? '?' + params : '');
    const opts = { method, credentials:'include', headers:{'Content-Type':'application/json'} };
    if (body) opts.body = JSON.stringify(body);
    const res  = await fetch(url, opts);
    const data = await res.json();
    if (res.status === 401) { showLoginScreen(); throw new Error('Session expirée.'); }
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
        addFilterBar();
        renderList();
    } catch (err) { toast('Erreur chargement : ' + err.message, 'error'); }
}

// ─────────────────────────────────────────────────────────────
//  RENDER — avec colonnes par statut + compteurs
// ─────────────────────────────────────────────────────────────
function renderList() {
    if (!listContainer) return;

    const filtered = filterPriority === 'all' ? tasks : tasks.filter(t => t.priority === filterPriority);

    if (filtered.length === 0) {
        listContainer.innerHTML = '<div class="empty-list">No tasks found.</div>';
    } else {
        // Grouper par statut
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

        // Drag & drop (admin only)
        if (isAdmin()) initDragDrop();
    }

    const active = tasks.filter(t => t.status !== 'done').length;
    const urgent = tasks.filter(t => t.priority === 'high' && t.status !== 'done').length;
    activeCount.textContent = active;
    urgentCount.textContent = urgent;
}

// ─────────────────────────────────────────────────────────────
//  DRAG & DROP
// ─────────────────────────────────────────────────────────────
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

// ─────────────────────────────────────────────────────────────
//  BUILD CARD
// ─────────────────────────────────────────────────────────────
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
                <a class="btn-sm btn-edit" href="modify_task.php?id=${task.id}" title="Modifier" style='text-decoration: none;'>MODIFIER✎</a>
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

// ─────────────────────────────────────────────────────────────
//  CRÉATION
// ─────────────────────────────────────────────────────────────
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

// ─────────────────────────────────────────────────────────────
//  TOAST
// ─────────────────────────────────────────────────────────────
function toast(msg, type = 'success') {
    const el = document.createElement('div');
    el.className = `toast toast--${type}`;
    const icon = { success: '✓', error: '✕', info: 'ℹ' }[type] ?? 'ℹ';
    el.innerHTML = `<span>${icon}</span><span>${msg}</span>`;
    toastCont.appendChild(el);
    setTimeout(() => { el.classList.add('hiding'); el.addEventListener('animationend', () => el.remove()); }, 3000);
}

// ─────────────────────────────────────────────────────────────
//  INIT
// ─────────────────────────────────────────────────────────────
showLoginScreen();