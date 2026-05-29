<?php

/* =============================================================================
   📝 FICHIER : src/Controller/ModifyTaskController.php — V3 FINALE
   =============================================================================
   
   🎯 RÔLE :
   Modifier une tâche existante.
   
   🔄 CHANGEMENTS vs. versions précédentes :
   - Utilise Symfony Security (denyAccessUnlessGranted, isGranted, getUser)
     au lieu de mon ancien AuthHelper.
   - Travaille avec l'Entity Task de Zeineb : pas de traduction movement→status
     ni tags→description. On utilise directement les termes musicaux.
   - Pour les tags : on les convertit en chaîne séparée par virgules pour le
     formulaire, et on reconvertit en tableau pour la BDD.
   - Suit la convention des routes de l'équipe : /tasks/{id}/edit
============================================================================= */

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class ModifyTaskController extends AbstractController
{
    /* =====================================================================
       MÉTHODE : edit() — affichage + sauvegarde
       =====================================================================
       
       Route : /tasks/{id}/edit  (nom : task_edit)
       
       Symfony injecte automatiquement :
         - $id      : extrait de l'URL
         - $request : tout ce que l'utilisateur a envoyé
         - $taskRepo : pour chercher la tâche dans la BDD
         - $em      : pour enregistrer les modifs
    ===================================================================== */

    #[Route('/tasks/{id}/edit', name: 'task_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(
        int $id,
        Request $request,
        TaskRepository $taskRepo,
        EntityManagerInterface $em
    ): Response {


        // ─────────────────────────────────────────────────────────────
        // ÉTAPE 1 : EXIGER UNE CONNEXION
        // ─────────────────────────────────────────────────────────────
        //
        // denyAccessUnlessGranted = "si l'utilisateur n'a pas ce rôle,
        // refuse l'accès". ROLE_USER signifie "connecté".
        //
        // 💡 C'est Symfony Security qui s'occupe de tout : si pas connecté,
        //    il redirige automatiquement vers /login.

        $this->denyAccessUnlessGranted('ROLE_USER');


        // ─────────────────────────────────────────────────────────────
        // ÉTAPE 2 : CHARGER LA TÂCHE
        // ─────────────────────────────────────────────────────────────

        $task = $taskRepo->find($id);

        if (!$task) {
            throw $this->createNotFoundException('Tâche introuvable (id = ' . $id . ').');
        }


        // ─────────────────────────────────────────────────────────────
        // ÉTAPE 3 : VÉRIFIER LES PERMISSIONS (Admin OU créateur)
        // ─────────────────────────────────────────────────────────────
        //
        // 💡 isGranted('ROLE_ADMIN') = "le user a-t-il le rôle Admin ?"
        // 💡 $this->getUser() = "objet User actuellement connecté"
        //
        // Règle : seul l'admin ou le créateur de la tâche peut accéder
        // à la modification (note : à voir avec ton prof si tu veux
        // ouvrir la page à tous, mais empêcher l'enregistrement).

        $isAdmin   = $this->isGranted('ROLE_ADMIN');
        $currentId = $this->getUser()?->getId();


        // ─────────────────────────────────────────────────────────────
        // ÉTAPE 4 : SI POST → ENREGISTRER LES MODIFICATIONS
        // ─────────────────────────────────────────────────────────────

        if ($request->isMethod('POST')) {

            // ─── Lire les valeurs envoyées ───
            $newTitle    = trim($request->request->get('title', ''));
            $newPriority = $request->request->get('priority', 'p');
            $newMovement = $request->request->get('movement', 'andante');
            $newDueDate  = trim($request->request->get('due_date', ''));
            $tagsRaw     = trim($request->request->get('tags', ''));


            // ─── Validation simple ───
            if ($newTitle === '') {
                $this->addFlash('error', 'Le titre est obligatoire.');
                return $this->render('modify_task/edit.html.twig', [
                    'task' => $task,
                ]);
            }


            // ─── Modifier les champs ───
            // 💡 Tous les utilisateurs connectés peuvent modifier le `movement`
            //    (le statut musical), mais SEUL l'Admin peut modifier le reste.

            $task->setMovement($newMovement);

            if ($isAdmin) {

                $task->setTitle($newTitle);
                $task->setPriority($newPriority);
                $task->setDueDate($newDueDate !== '' ? $newDueDate : null);

                // Convertir la chaîne "symfony, php, urgent" en tableau ["symfony", "php", "urgent"]
                $tagsArray = $tagsRaw !== ''
                    ? array_map('trim', explode(',', $tagsRaw))
                    : [];
                $task->setTags($tagsArray);
            }


            // ─── Sauvegarder en BDD ───
            $em->flush();

            $this->addFlash('success', 'Tâche "' . $task->getTitle() . '" modifiée !');

            return $this->redirectToRoute('task_list');
        }


        // ─────────────────────────────────────────────────────────────
        // ÉTAPE 5 : AFFICHER LE FORMULAIRE (GET)
        // ─────────────────────────────────────────────────────────────

        return $this->render('modify_task/edit.html.twig', [
            'task' => $task,
        ]);
    }
}