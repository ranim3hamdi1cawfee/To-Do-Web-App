

var periodeActive = "today";

/* Filtrer par période */
function getTachesParPeriode(periode) {
  var taches = window.TACHES_PHP || [];
  var auj = new Date();
  auj.setHours(0, 0, 0, 0);

  return taches.filter(function(tache) {
    var dateStr   = (tache.date || "").slice(0, 10);
    var dateTache = new Date(dateStr + "T00:00:00");

    if (periode === "today") {
      return dateStr === new Date().toISOString().slice(0, 10);
    }
    if (periode === "week") {
      var lundi = new Date(auj);
      var j = auj.getDay();
      lundi.setDate(auj.getDate() + (j === 0 ? -6 : 1 - j));
      return dateTache >= lundi;
    }
    if (periode === "month") {
      return dateTache.getMonth()    === auj.getMonth() &&
             dateTache.getFullYear() === auj.getFullYear();
    }
    if (periode === "year") {
      return dateTache.getFullYear() === auj.getFullYear();
    }
    return false;
  });
}

function estTerminee(tache) {
  return tache.faite === true || tache.status === 'done';
}

/* Calculer le top user sur la période  */
function getTopUser(taches) {
  var compteur = {};
  taches.forEach(function(t) {
    if (estTerminee(t) && t.created_by) {
      var uid = t.created_by;
      compteur[uid] = (compteur[uid] || 0) + 1;
    }
  });

  var topId = null, topCount = 0;
  Object.keys(compteur).forEach(function(uid) {
    if (compteur[uid] > topCount) {
      topCount = compteur[uid];
      topId    = uid;
    }
  });

  return topCount > 0 ? { id: topId, count: topCount } : null;
}

/*  Mettre à jour la page  */
function mettreAJourPage() {
  var taches    = getTachesParPeriode(periodeActive);
  var total     = taches.length;
  var faites    = taches.filter(estTerminee).length;
  var restantes = total - faites;
  var taux      = total > 0 ? Math.round((faites / total) * 100) : 0;

  /* KPI */
  document.getElementById("kpi-total").textContent     = total;
  document.getElementById("kpi-faites").textContent    = faites;
  document.getElementById("kpi-restantes").textContent = restantes;
  document.getElementById("kpi-taux").textContent      = taux + "%";

  /* Barre */
  document.getElementById("barre-progress").style.width     = taux + "%";
  document.getElementById("legende-faites").textContent     = faites + " Done";
  document.getElementById("legende-restantes").textContent  = restantes + " Todo-Doing";
  document.getElementById("legende-pct").textContent        = taux + "%";

  /* Top user sur la période (met à jour le compteur dans le bandeau) */
  var topEl = document.getElementById("top-count");
  if (topEl) {
    var top = getTopUser(taches);
    topEl.textContent = top ? top.count : 0;
  }

  dessinerGraphique(taches);
}

/* Graphique */
function dessinerGraphique(taches) {
  var conteneur = document.getElementById("graphique");
  var axeY      = document.getElementById("axe-y");
  var titrGraph = document.getElementById("titre-graphique");
  conteneur.innerHTML = "";
  axeY.innerHTML      = "";

  var groupes = [];

  if (periodeActive === "today") {
    titrGraph.textContent = "Task per hour - Today";
    conteneur.className   = "graphique mode-jour";
    for (var h = 0; h < 24; h++) groupes.push({ label: h + "h", faites: 0, reste: 0 });
    taches.forEach(function(t) {
      var h = typeof t.heure === 'number' ? t.heure : 0;
      h = Math.min(Math.max(h, 0), 23);
      if (estTerminee(t)) groupes[h].faites++; else groupes[h].reste++;
    });
  }
  else if (periodeActive === "week") {
    titrGraph.textContent = "Task per day — This week";
    conteneur.className   = "graphique mode-semaine";
    ["Mon","Tue","Wed","Thu","Fri","Sat","Sun"].forEach(function(j) {
      groupes.push({ label: j, faites: 0, reste: 0 });
    });
    taches.forEach(function(t) {
      var numJS = new Date((t.date || "").slice(0,10) + "T00:00:00").getDay();
      var idx   = numJS === 0 ? 6 : numJS - 1;
      if (estTerminee(t)) groupes[idx].faites++; else groupes[idx].reste++;
    });
  }
  else if (periodeActive === "month") {
    titrGraph.textContent = "Task per day — This Month";
    conteneur.className   = "graphique mode-mois";
    var auj = new Date();
    var nbJ = new Date(auj.getFullYear(), auj.getMonth() + 1, 0).getDate();
    for (var d = 1; d <= nbJ; d++) groupes.push({ label: String(d), faites: 0, reste: 0 });
    taches.forEach(function(t) {
      var idx = new Date((t.date || "").slice(0,10) + "T00:00:00").getDate() - 1;
      if (idx >= 0 && idx < groupes.length) {
        if (estTerminee(t)) groupes[idx].faites++; else groupes[idx].reste++;
      }
    });
  }
  else if (periodeActive === "year") {
    titrGraph.textContent = "Task per month — This Year";
    conteneur.className   = "graphique mode-annee";
    ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"].forEach(function(m) {
      groupes.push({ label: m, faites: 0, reste: 0 });
    });
    taches.forEach(function(t) {
      var idx = new Date((t.date || "").slice(0,10) + "T00:00:00").getMonth();
      if (estTerminee(t)) groupes[idx].faites++; else groupes[idx].reste++;
    });
  }

  var maxVal = 1;
  groupes.forEach(function(g) {
    if (g.faites + g.reste > maxVal) maxVal = g.faites + g.reste;
  });

  var hauteur = (conteneur.clientHeight || 220) - 22;
  if (hauteur < 30) hauteur = 200;

  for (var i = 4; i >= 0; i--) {
    var grad = document.createElement("span");
    grad.className   = "graduation-y";
    grad.textContent = Math.round((i / 4) * maxVal);
    axeY.appendChild(grad);
  }

  groupes.forEach(function(grp, index) {
    var hautFaites = Math.round((grp.faites / maxVal) * hauteur);
    var hautReste  = Math.round((grp.reste  / maxVal) * hauteur);
    var classeLabel = "label-colonne" +
      (periodeActive === "month" && index % 2 !== 0 ? " cache" : "");

    var col = document.createElement("div");
    col.className = "groupe-colonne";
    col.innerHTML =
      '<div class="barres-duo">' +
        '<div class="barre-graph faites" style="height:' + hautFaites + 'px"' +
             ' data-info="' + grp.faites + ' done"></div>' +
        '<div class="barre-graph a-faire" style="height:' + hautReste + 'px"' +
             ' data-info="' + grp.reste + ' todo/doing"></div>' +
      '</div>' +
      '<span class="' + classeLabel + '">' + grp.label + '</span>';
    conteneur.appendChild(col);
  });
}

/* Changer de période */
function changerPeriode(periode, bouton) {
  periodeActive = periode;
  document.querySelectorAll(".btn-periode").forEach(function(b) {
    b.classList.remove("actif");
  });
  bouton.classList.add("actif");
  mettreAJourPage();
}

window.addEventListener("DOMContentLoaded", function() {
  mettreAJourPage();
});