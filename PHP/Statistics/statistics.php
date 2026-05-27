<?php

session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: index.html');
    exit();
}

require_once 'db.php';

$user_id  = $_SESSION['user_id'];
$username = htmlspecialchars($_SESSION['username'] ?? '');
$role     = $_SESSION['role'] ?? 'Regular';

$erreur   = '';
$taches   = [];
$groupes  = [];
$user_group_id   = null;
$user_group_name = '';

try {
    $db = getDB();

    // user info + group
   
    $stmtUser = $db->prepare("
        SELECT u.id, u.username, u.role, u.group_id, g.name AS group_name
        FROM user u
        LEFT JOIN `group` g ON g.id = u.group_id
        WHERE u.id = :id
        LIMIT 1
    ");

    $stmtUser->execute([':id' => $user_id]);
    $userInfo = $stmtUser->fetch();

    if ($userInfo) {
        $user_group_id   = $userInfo['group_id'];
        $user_group_name = htmlspecialchars($userInfo['group_name'] ?? 'Aucun groupe');

        if (!empty($userInfo['role'])) {
            $role = $userInfo['role'];
        }
    }

    // group admin
    if ($role === 'Admin') {
        $stmtGroupes = $db->query("SELECT id, name FROM `group` ORDER BY name ASC");
        $groupes = $stmtGroupes->fetchAll();
    }

} catch (PDOException $e) {
    $erreur = 'Erreur base user : ' . $e->getMessage();
}

try {
    $db = getDB();

    // task
    $stmt = $db->prepare("
        SELECT id, title, priority, movement, tags, due_date
        FROM task
        ORDER BY id DESC
    ");

    $stmt->execute();
    $taches_brutes = $stmt->fetchAll();

    foreach ($taches_brutes as $t) {
        $taches[] = [
            'id'       => $t['id'],
            'titre'    => $t['title'],
            'priority' => $t['priority'],
            'movement' => $t['movement'],
            'tags'     => json_decode($t['tags'], true),
            'due_date' => $t['due_date'],
        ];
    }

} catch (PDOException $e) {
    $erreur = 'Erreur base tâches : ' . $e->getMessage();
    $taches = [];
}

$taches_json = json_encode($taches, JSON_UNESCAPED_UNICODE);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Statistiques — TaskFlow</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Mono:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="statistics.css" />
</head>
<body>
 
  <div class="noise"></div>
 
  
  <header>
    <div class="header-left">
      <div class="logo-mark">TF</div>
      <div>
        <h1 id="TaskFlow">TaskFlow</h1>
        <span class="mode-badge">● Statistics</span>
      </div>
    </div>
    <div class="header-right">
      <!-- Info utilisateur connecté -->
      <div class="stat-pill">
        <span class="stat-num" style="font-size:0.75rem;">
          <?= $username ?>
        </span>
        <span class="stat-label"><?= $role ?></span>
      </div>
 
      <?php if ($role === 'Regular' && $user_group_name): ?>
      <!-- Groupe du Regular -->
      <div class="stat-pill stat-pill--accent">
        <span class="stat-num" style="font-size:0.7rem;">
          <?= $user_group_name ?>
        </span>
        <span class="stat-label">Group</span>
      </div>
      <?php endif; ?>
 
      <button
        onclick="window.location.href='index.html'"
        style="background:none;border:1px solid var(--border);border-radius:6px;
               color:var(--muted);cursor:pointer;font-family:var(--font-mono);
               font-size:11px;padding:4px 14px;transition:.2s;"
        onmouseover="this.style.borderColor='var(--accent)';this.style.color='var(--accent)'"
        onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--muted)'">
        ← Accueil
      </button>
    </div>
  </header>
 
  <main>
 
    <?php if ($erreur): ?>
      <div style="background:rgba(255,80,80,0.1);border:1px solid #ff5555;
                  border-radius:10px;padding:12px 16px;color:#ff5555;font-size:0.9rem;">
         <?= htmlspecialchars($erreur) ?>
      </div>
    <?php endif; ?>
 
    
    <div class="ligne-principale">
 
      <div class="periode-section">
        <button class="btn-periode actif" data-p="jour"
                onclick="changerPeriode('today', this)">
          <span class="periode-icon">◎</span>Today
        </button>
        <button class="btn-periode" data-p="semaine"
                onclick="changerPeriode('week', this)">
          <span class="periode-icon">◫</span>this Week
        </button>
        <button class="btn-periode" data-p="mois"
                onclick="changerPeriode('month', this)">
          <span class="periode-icon">◳</span>This Month
        </button>
        <button class="btn-periode" data-p="annee"
                onclick="changerPeriode('year', this)">
          <span class="periode-icon">◈</span>This Year
        </button>
      </div>
 
      <div class="cartes-kpi">
        <div class="kpi-card bleu">
          <span class="kpi-valeur" id="kpi-total">0</span>
          <span class="kpi-label">Total tasks</span>
        </div>
        <div class="kpi-card vert">
          <span class="kpi-valeur" id="kpi-faites">0</span>
          <span class="kpi-label">Done</span>
        </div>
        <div class="kpi-card orange">
          <span class="kpi-valeur" id="kpi-restantes">0</span>
          <span class="kpi-label">Todo-Doing</span>
        </div>
        <div class="kpi-card rouge">
          <span class="kpi-valeur" id="kpi-taux">0%</span>
          <span class="kpi-label">Tasks Completed</span>
        </div>
      </div>
 
    </div>

    <div class="contexte-bandeau">
      <?php if ($role === 'Admin'): ?>
        <span class="ctx-badge ctx-admin">Admin — Overall Statistics</span>
      <?php else: ?>
        <span class="ctx-badge ctx-regular">
           Regular View — Group : <?= $user_group_name ?: 'Not assigned' ?>
        </span>
      <?php endif; ?>
    </div>
 
    <div class="section-barre">
      <span class="barre-titre">Overall Progress</span>
      <div class="barre-fond">
        <div class="barre-remplie" id="barre-progress" style="width:0%"></div>
      </div>
      <div class="barre-legende">
        <span id="legende-faites">0 Done</span>
        <span class="barre-pct" id="legende-pct">0%</span>
        <span id="legende-restantes">0 Todo-Doing</span>
      </div>
    </div>
 
    <div class="section-graphique">
      <div class="graphique-entete">
        <span class="graphique-titre" id="titre-graphique">Task per hour</span>
        <div class="legende-graphique">
          <div class="legende-item">
            <div class="legende-couleur" style="background:var(--accent,#c8fb4b)"></div>
            <span>Done</span>
          </div>
          <div class="legende-item">
            <div class="legende-couleur" style="background:var(--muted);opacity:0.4"></div>
            <span>Todo-Doing</span>
          </div>
        </div>
      </div>
      <div class="graphique-zone">
        <div class="axe-y"     id="axe-y"></div>
        <div class="graphique" id="graphique"></div>
      </div>
    </div>
 
  </main>
 
  <script>
    window.TACHES_PHP = <?= $taches_json ?>;
    window.USER_ROLE  = "<?= $role ?>";
  </script>
  <script src="statistics.js"></script>
 
</body>
</html>