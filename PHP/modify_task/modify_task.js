/*
   modify_task.js
   Lit le formulaire et envoie les données à update_task.php
    */


// Récupérer les infos passées par PHP
var role   = window.MT_ROLE;       // 'Admin' ou 'Regular'
var taskId = window.MT_TASK_ID;    // un nombre


// Récupérer les éléments HTML
var saveBtn  = document.getElementById('mt-save-btn');
var feedback = document.getElementById('mt-feedback');
var form     = document.getElementById('mt-form');


// Quand on clique sur "Save"
saveBtn.addEventListener('click', saveTask);


// Fonction principale : sauvegarder la tâche
function saveTask() {

    // Lire le status (toujours modifiable)
    var statusRadio = form.querySelector('input[name="status"]:checked');
    var status = statusRadio.value;

    // Préparer les données à envoyer
    var data = {
        id: taskId,
        status: status
    };

    // Si Admin, on ajoute les autres champs
    if (role === 'Admin') {

        data.title       = document.getElementById('mt-title').value.trim();
        data.description = document.getElementById('mt-desc').value.trim();
        data.due_date    = document.getElementById('mt-due').value;

        var priorityRadio = form.querySelector('input[name="priority"]:checked');
        data.priority = priorityRadio.value;

        // Petite vérification : le titre ne doit pas être vide
        if (data.title === '') {
            showFeedback('Le titre est obligatoire.', 'error');
            return;
        }
    }

    // Demander confirmation
    if (!confirm('Sauvegarder les modifications ?')) {
        return;
    }

    // Désactiver le bouton pendant l'envoi
    saveBtn.disabled = true;

    // Envoyer les données à update_task.php
    fetch('update_task.php', {
        method: 'POST',
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(result) {

        if (result.success === true) {
            // Succès : afficher message et retourner au board
            showFeedback(result.message, 'success');

            setTimeout(function() {
                window.location.href = 'index.html';
            }, 1500);
        }
        else {
            // Erreur : afficher le message du serveur
            showFeedback(result.error, 'error');
            saveBtn.disabled = false;
        }
    })
    .catch(function(err) {
        // Erreur réseau (XAMPP arrêté, etc.)
        showFeedback('Erreur réseau. Vérifie que XAMPP tourne.', 'error');
        saveBtn.disabled = false;
    });
}


// Afficher un message en haut du formulaire
function showFeedback(message, type) {

    feedback.style.display = 'block';

    if (type === 'success') {
        feedback.className = 'mt-feedback mt-feedback--success';
        feedback.textContent = '✓ ' + message;
    }
    else {
        feedback.className = 'mt-feedback mt-feedback--error';
        feedback.textContent = '✕ ' + message;
    }
}