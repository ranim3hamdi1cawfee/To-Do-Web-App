<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_logic/login.php");
    exit();
}

require_once __DIR__ . '/../add-task/db.php';

$user_id  = $_SESSION['user_id'];
$role     = $_SESSION['role'] ?? 'Regular';
$username = '';
$erreur   = '';
$taches   = [];
$user_group_id   = null;
$user_group_name = '';
 
//Infos de l'utilisateur connecte
try {
    $db = getDB();

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
        $username        = htmlspecialchars($userInfo['username']);
        $user_group_id   = $userInfo['group_id'];
        $user_group_name = htmlspecialchars($userInfo['group_name'] ?? '');
        if (!empty($userInfo['role'])) {
            $role = $userInfo['role'];
            $_SESSION['role'] = $role;
        }
    }

} catch (PDOException $e) {
    $erreur = 'User query error: ' . $e->getMessage();
}

//Charger les taches selon le role

try {
    $db = getDB();

    // si admin, fetch everything. si regular, filter by group or default to everything if group is null
    if ($role === 'Admin' || $user_group_id === null) {
        $stmt = $db->prepare("
            SELECT t.id, t.title, t.priority, t.movement, t.due_date, t.group_id, g.name AS group_name
            FROM task t
            LEFT JOIN `group` g ON g.id = t.group_id
            ORDER BY t.id DESC
        ");
        $stmt->execute();
    } else {
        $stmt = $db->prepare("
            SELECT t.id, t.title, t.priority, t.movement, t.due_date, t.group_id, g.name AS group_name
            FROM task t
            LEFT JOIN `group` g ON g.id = t.group_id
            WHERE t.group_id = :group_id
            ORDER BY t.id DESC
        ");
        $stmt->execute([':group_id' => $user_group_id]);
    }

    $taches_brutes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($taches_brutes as $t) {
        $taches[] = [
            'id'                  => intval($t['id']),
            'titre'               => strval($t['title']),
            'priority'            => strval($t['priority']),
            'movement'            => strval($t['movement']),
            'data_status'         => strval($t['movement']), 
            'faite' => ($t['movement'] === 'allegro'),
            'date'                => !empty($t['due_date']) ? strval($t['due_date']) : '',
            'due_date'            => !empty($t['due_date']) ? strval($t['due_date']) : '',
            'group_id'            => $t['group_id'] !== null ? intval($t['group_id']) : null,
            'group_name'          => $t['group_name'] ? strval($t['group_name']) : ''
        ];
    }

} catch (PDOException $e) {
    $erreur = 'Tasks query error: ' . $e->getMessage();
    $taches = [];
}

// Secure HEX encoding flags to prevent HTML/Quote injection breakdown inside JavaScript tags
$taches_json = json_encode($taches, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
if ($taches_json === false) {
    $taches_json = '[]';
    $erreur = 'JSON encoding failed: ' . json_last_error_msg();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Statistics — TaskFlow</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Mono:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="statistics.css" />
  <link rel="stylesheet" href="../../css/style.css" />
</head>
<body>

  <?php require_once("../welcome/navbar.php"); ?>
  <div class="noise"></div>

  <main>

    <?php if ($erreur): ?>
      <div style="background:rgba(255,80,80,0.1);border:1px solid #ff5555; border-radius:10px;padding:12px 16px;color:#ff5555;font-size:0.9rem;margin-bottom:16px;">
        ⚠ <?= htmlspecialchars($erreur) ?>
      </div>
    <?php endif; ?>

    <div class="ligne-principale">
      <div class="periode-section">
        <button class="btn-periode actif" data-p="today" onclick="changerPeriode('today', this)">
          <span class="periode-icon">◎</span>Today
        </button>
        <button class="btn-periode" data-p="week" onclick="changerPeriode('week', this)">
          <span class="periode-icon">◫</span>This Week
        </button>
        <button class="btn-periode" data-p="month" onclick="changerPeriode('month', this)">
          <span class="periode-icon">◳</span>This Month
        </button>
        <button class="btn-periode" data-p="year" onclick="changerPeriode('year', this)">
          <span class="periode-icon">◈</span>This Year
        </button>
      </div>

      <div class="cartes-kpi">
        <div class="kpi-card bleu">
          <span class="kpi-valeur" id="kpi-total">0</span>
          <span class="kpi-label">Total Tasks</span>
        </div>
        <div class="kpi-card vert">
          <span class="kpi-valeur" id="kpi-faites">0</span>
          <span class="kpi-label">Done</span>
        </div>
        <div class="kpi-card orange">
          <span class="kpi-valeur" id="kpi-restantes">0</span>
          <span class="kpi-label">Todo + Doing</span>
        </div>
        <div class="kpi-card rouge">
          <span class="kpi-valeur" id="kpi-taux">0%</span>
          <span class="kpi-label">Completion</span>
        </div>
      </div>
    </div>

    <div class="contexte-bandeau">
      <?php if ($role === 'Admin'): ?>
        <span class="ctx-badge ctx-admin"> Admin — Global Statistics</span>
      <?php else: ?>
        <span class="ctx-badge ctx-regular">
           Regular — Group: <?= $user_group_name ?: 'Not assigned' ?>
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
        <span id="legende-restantes">0 Todo/Doing</span>
      </div>
    </div>

    <div class="section-graphique">
      <div class="graphique-entete">
        <span class="graphique-titre" id="titre-graphique">Tasks due today</span>
        <div class="legende-graphique">
          <div class="legende-item">
            <div class="legende-couleur" style="background:var(--accent,#c8fb4b)"></div>
            <span>Done</span>
          </div>
          <div class="legende-item">
            <div class="legende-couleur" style="background:var(--muted);opacity:0.4"></div>
            <span>Todo/Doing</span>
          </div>
        </div>
      </div>
      <div class="graphique-zone">
        <div class="axe-y" id="axe-y"></div>
        <div class="graphique" id="graphique"></div>
      </div>
    </div>

  </main>

  <script>
    // Injected safely using HEX tags
    window.TACHES_PHP    = <?= $taches_json ?>;
    window.USER_ROLE     = "<?= $role ?>";
    window.USER_GROUP_ID = <?= json_encode($user_group_id) ?>;

    // Diagnostics test logger
    console.log("=== TaskFlow Debug Stats ===");
    console.log("Current User Role:", window.USER_ROLE);
    console.log("Current Group ID:", window.USER_GROUP_ID);
    console.log("Loaded Tasks Payload Array Size:", window.TACHES_PHP.length);
    console.log("Full Raw Data Array Content:", window.TACHES_PHP);
  </script>
  <script src="statistics.js"></script>

</body>
</html>
