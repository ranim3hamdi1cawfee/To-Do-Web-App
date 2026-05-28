var periodeActive = "today";

function getTachesParPeriode(periode) {
  var toutesLesTouches = window.TACHES_PHP || [];
  var role     = window.USER_ROLE     || 'Regular';
  var groupId  = window.USER_GROUP_ID || null;

  var taches = toutesLesTouches;
  
  // Filtrage par groupe pour les utilisateurs réguliers
  if (role !== 'Admin' && groupId !== null) {
    taches = toutesLesTouches.filter(function(t) {
      return t.group_id == groupId;
    });
  }

  // Obtenir les composants de la date d'aujourd'hui en heure locale
  var auj = new Date();
  var aujAnnee = auj.getFullYear();
  var aujMois  = auj.getMonth(); 
  var aujJour  = auj.getDate();

  // Définir les limites de la semaine courante 
  var jourSemaine = auj.getDay(); 
  var decalageLundi = (jourSemaine === 0) ? -6 : 1 - jourSemaine;
  
  var lundi = new Date(aujAnnee, aujMois, aujJour + decalageLundi, 0, 0, 0, 0);
  var dimanche = new Date(aujAnnee, aujMois, aujJour + decalageLundi + 6, 23, 59, 59, 999);

  return taches.filter(function(tache) {
    var bruteDate = tache.date || tache.due_date || "";
    if (!bruteDate) return false;

    // Découpage et nettoyage de la chaîne de date (gère '2026-04-01' et '2026-4-1')
    var segments = bruteDate.trim().split('-');
    if (segments.length !== 3) return false;

    var tAnnee = parseInt(segments[0], 10);
    var tMois  = parseInt(segments[1], 10) - 1; 
    var tJour  = parseInt(segments[2], 10);

    // Instanciation de la date de la tâche à midi pour éviter les sauts de fuseau horaire
    var dateObj = new Date(tAnnee, tMois, tJour, 12, 0, 0, 0);
    
    // Sauvegarder la version normalisée pour l'affichage des graphiques
    tache.date = tAnnee + "-" + String(tMois + 1).padStart(2, '0') + "-" + String(tJour).padStart(2, '0');

    // 1. Filtrage : Aujourd'hui
    if (periode === "today") {
      return tAnnee === aujAnnee && tMois === aujMois && tJour === aujJour;
    }

    // filtrage : week
    if (periode === "week") {
      return dateObj >= lundi && dateObj <= dimanche;
    }

    // filtrage : month
    if (periode === "month") {
      return tAnnee === aujAnnee && tMois === aujMois;
    }

    // filtrage : year
    if (periode === "year") {
      return tAnnee === aujAnnee;
    }

    return false;
  });
}

function estTerminee(tache) {
  return tache.faite === true || tache.movement === 'andante';
}

/* Mettre à jour toute la page */
function mettreAJourPage() {
  var taches    = getTachesParPeriode(periodeActive);
  var total     = taches.length;
  var faites    = taches.filter(estTerminee).length;
  var restantes = total - faites;
  var taux      = total > 0 ? Math.round((faites / total) * 100) : 0;

  /* Affichage des KPIs */
  document.getElementById("kpi-total").textContent     = total;
  document.getElementById("kpi-faites").textContent    = faites;
  document.getElementById("kpi-restantes").textContent = restantes;
  document.getElementById("kpi-taux").textContent      = taux + "%";

  /* Affichage de la barre de progression */
  document.getElementById("barre-progress").style.width    = taux + "%";
  document.getElementById("legende-faites").textContent    = faites + " Done";
  document.getElementById("legende-restantes").textContent = restantes + " Todo/Doing";
  document.getElementById("legende-pct").textContent       = taux + "%";

  dessinerGraphique(taches);
}

/* Dessiner le graphique dynamique */
function dessinerGraphique(taches) {
  var conteneur = document.getElementById("graphique");
  var axeY      = document.getElementById("axe-y");
  var titrGraph = document.getElementById("titre-graphique");
  if(!conteneur || !axeY || !titrGraph) return;

  conteneur.innerHTML = "";
  axeY.innerHTML      = "";

  var groupes = [];

  if (periodeActive === "today") {
    titrGraph.textContent = "Tasks per hour — Today";
    conteneur.className   = "graphique mode-semaine";
    ["Todo\n", "Doing\n", "Done\n"].forEach(function(l) {
      groupes.push({ label: l, faites: 0, reste: 0 });
    });
    taches.forEach(function(t) {
      if (t.movement === 'andante')  groupes[2].faites++;
      else if (t.movement === 'moderato') groupes[1].reste++;
      else                                groupes[0].reste++;
    });
  }
  else if (periodeActive === "week") {
    titrGraph.textContent = "Tasks per day — This Week";
    conteneur.className   = "graphique mode-semaine";
    ["Mon","Tue","Wed","Thu","Fri","Sat","Sun"].forEach(function(j) {
      groupes.push({ label: j, faites: 0, reste: 0 });
    });
    taches.forEach(function(t) {
      var segments = t.date.split('-');
      var numJS = new Date(parseInt(segments[0],10), parseInt(segments[1],10)-1, parseInt(segments[2],10), 12, 0, 0).getDay();
      var idx   = (numJS === 0) ? 6 : numJS - 1;
      if(idx >= 0 && idx < 7) {
        if (estTerminee(t)) groupes[idx].faites++;
        else                groupes[idx].reste++;
      }
    });
  }
  else if (periodeActive === "month") {
    titrGraph.textContent = "Tasks by day — This Month";
    conteneur.className   = "graphique mode-mois";
    var auj = new Date();
    var nbJ = new Date(auj.getFullYear(), auj.getMonth() + 1, 0).getDate();
    for (var d = 1; d <= nbJ; d++) groupes.push({ label: String(d), faites: 0, reste: 0 });
    taches.forEach(function(t) {
      var segments = t.date.split('-');
      var idx = parseInt(segments[2], 10) - 1;
      if (idx >= 0 && idx < groupes.length) {
        if (estTerminee(t)) groupes[idx].faites++;
        else                groupes[idx].reste++;
      }
    });
  }
  else if (periodeActive === "year") {
    titrGraph.textContent = "Tasks per month — This Year";
    conteneur.className   = "graphique mode-annee";
    ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"].forEach(function(m) {
      groupes.push({ label: m, faites: 0, reste: 0 });
    });
    taches.forEach(function(t) {
      var segments = t.date.split('-');
      var idx = parseInt(segments[1], 10) - 1;
      if (idx >= 0 && idx < 12) {
        if (estTerminee(t)) groupes[idx].faites++;
        else                groupes[idx].reste++;
      }
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
    var classeLabel = "label-colonne" + (periodeActive === "month" && index % 2 !== 0 ? " cache" : "");

    var col = document.createElement("div");
    col.className = "groupe-colonne";
    col.innerHTML =
      '<div class="barres-duo">' +
        '<div class="barre-graph faites" style="height:' + hautFaites + 'px"' + ' data-info="' + grp.faites + ' done (andante)"></div>' +
        '<div class="barre-graph a-faire" style="height:' + hautReste + 'px"' + ' data-info="' + grp.reste + ' todo/doing"></div>' +
      '</div>' +
      '<span class="' + classeLabel + '">' + grp.label + '</span>';
    conteneur.appendChild(col);
  });
}

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
