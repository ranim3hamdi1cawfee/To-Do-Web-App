<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\GroupRepository;

class TaskController extends AbstractController
{
    // ─────────────────────────────────────────────────────────────
    //  GET /tasks — liste les tâches DE L'UTILISATEUR CONNECTÉ.
    //
    //  $this->getUser() → retourne l'objet User connecté via la session Symfony.
    //  C'est l'équivalent de $_SESSION['user_id'] dans l'ancienne api.php,
    //  mais Symfony retourne directement l'objet User complet.
    //
    //  On passe ce User à findByUser() pour ne récupérer QUE ses tâches.
    //  Équivalent du filtre WHERE group_id = ? dans l'ancienne api.php.
    // ─────────────────────────────────────────────────────────────
    #[Route('/tasks', name: 'task_list')]
    public function index(
        Request $request,
        TaskRepository $taskRepository,
        GroupRepository $groupRepository
    ): Response {
        $selectedGroup = $request->query->get('group');

        if ($selectedGroup) {

            $tasks = $taskRepository->findBy([
                'groupId' => $selectedGroup
            ]);

        } else {

            $tasks = $taskRepository->findAll();

        }

        $groups = $groupRepository->findAll();

        $activeCount = 0;

        foreach ($tasks as $task) {

            if ($task->getStatus() != 'done') {
                $activeCount++;
            }

        }

        $urgentCount = 0;

        foreach ($tasks as $task) {

            if (
                $task->getPriority() == 'high'
                && $task->getStatus() != 'done'
            ) {

                $urgentCount++;

            }

        }
        return $this->render('task/index.html.twig', [

            'tasks' => $tasks,

            'groups' => $groups,

            'selectedGroup' => $selectedGroup,

            'activeCount' => $activeCount,

            'urgentCount' => $urgentCount

        ]);
    }

    // ─────────────────────────────────────────────────────────────
    //  GET|POST /tasks/add — création d'une tâche.
    //  Accessible à tous les users connectés (ROLE_USER).
    //  La tâche est automatiquement liée à l'utilisateur connecté.
    // ─────────────────────────────────────────────────────────────
    #[Route('/tasks/add', name: 'task_add')]
    public function add(Request $request, EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Traitement manuel du champ tags (mapped: false).
            // explode → découpe "php, urgent" en ['php', 'urgent'].
            // array_map('trim', ...) → supprime les espaces autour de chaque tag.
            $tagsRaw = $form->get('tags')->getData();
            $task->setTags(
                $tagsRaw ? array_map('trim', explode(',', $tagsRaw)) : []
            );

            // ─────────────────────────────────────────────────────
            //  LIER LA TÂCHE À L'UTILISATEUR CONNECTÉ.
            //
            //  $this->getUser() → retourne l'objet User de la session.
            //  $task->setUser(...) → remplit la colonne user_id en BDD.
            //
            //  Sans cette ligne → ForeignKeyConstraintViolationException
            //  car user_id est NOT NULL dans la table task.
            //  C'est exactement l'erreur qu'on avait avant !
            // ─────────────────────────────────────────────────────
            $task->setUser($this->getUser());

            // persist() → Doctrine surveille cet objet (prépare un INSERT).
            $manager->persist($task);

            // flush() → exécute le INSERT SQL avec user_id rempli.
            $manager->flush();

            $this->addFlash('success', 'Tâche "' . $task->getTitle() . '" ajoutée !');
            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    //  GET /tasks/delete/{id} — suppression (ADMIN ONLY).
    //
    //  Symfony résout {id} en objet Task via le ParamConverter.
    //  On vérifie en plus que la tâche appartient bien à l'utilisateur
    //  connecté OU que c'est un admin (sécurité double).
    // ─────────────────────────────────────────────────────────────
    #[Route('/tasks/delete/{id}', name: 'task_delete')]
    public function delete(Task $task, EntityManagerInterface $manager): Response
    {
        // Seul un Admin peut supprimer — équivalent de requireAdmin() dans api.php.
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Vérification supplémentaire : la tâche doit appartenir au même groupe/user.
        // Un admin ne devrait pas pouvoir supprimer les tâches d'un autre groupe.
        // getUser() → l'admin connecté. $task->getUser() → le propriétaire de la tâche.
        // Pour l'instant on autorise l'admin à supprimer toutes les tâches (comme dans api.php).
        $manager->remove($task);
        $manager->flush();

        $this->addFlash('success', 'Tâche supprimée.');
        return $this->redirectToRoute('task_list');
    }

    // ─────────────────────────────────────────────────────────────
    //  GET /tasks/status/{id}/{movement} — changer le statut (ADMIN ONLY).
    //  Équivalent du case 'PUT' + changeStatus() dans index.js/api.php.
    // ─────────────────────────────────────────────────────────────
    #[Route('/tasks/status/{id}/{movement}', name: 'task_change_status', methods: ['GET'])]
    public function changeStatus(Task $task, string $movement, EntityManagerInterface $manager): Response
    {
        // Seul un Admin peut changer le statut — équivalent de requireAdmin() dans api.php.
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Liste blanche des valeurs autorisées pour éviter des valeurs arbitraires dans l'URL.
        $allowed = ['andante', 'allegro', 'adagio', 'presto'];
        if (!in_array($movement, $allowed, true)) {
            $this->addFlash('error', 'Mouvement invalide.');
            return $this->redirectToRoute('task_list');
        }

        // Mise à jour du mouvement.
        // Pas de persist() car $task est déjà "managé" par Doctrine (chargé via ParamConverter).
        // flush() suffit pour déclencher l'UPDATE SQL.
        $task->setMovement($movement);
        $manager->flush();

        $this->addFlash('success', 'Statut mis à jour.');
        return $this->redirectToRoute('task_list');
    }

    // ─────────────────────────────────────────────────────────────
    //  GET|POST /tasks/edit/{id} — modification (ADMIN ONLY).
    //  Équivalent du bouton MODIFY✎ dans index.js.
    // ─────────────────────────────────────────────────────────────
    #[Route('/tasks/edit/{id}', name: 'task_edit')]
    public function edit(Task $task, Request $request, EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Réutilise TaskType avec $task déjà peuplé → Symfony pré-remplit les champs.
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tagsRaw = $form->get('tags')->getData();
            $task->setTags(
                $tagsRaw ? array_map('trim', explode(',', $tagsRaw)) : []
            );

            // Pas de persist() : $task existe déjà en BDD, Doctrine détecte les changements.
            // flush() déclenche l'UPDATE SQL.
            $manager->flush();

            $this->addFlash('success', 'Tâche "' . $task->getTitle() . '" modifiée !');
            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
            'tags_default' => implode(', ', $task->getTags()),
        ]);
    }
    #[Route('/tasks/complete/{id}', name: 'task_complete')]
    public function complete(
        Task $task,
        EntityManagerInterface $manager
    ): Response {
        $task->setStatus('done');

        $manager->flush();

        return $this->redirectToRoute('task_list');
    }
    #[Route('/tasks/restore/{id}', name: 'task_restore')]
    public function restore(
        Task $task,
        EntityManagerInterface $em
    ): Response {

        $task->setStatus('in progress');

        $em->flush();

        return $this->redirectToRoute('task_list');
    }
}