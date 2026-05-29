<?php

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
    #[Route('/tasks/{id}/edit', name: 'task_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(
        int $id,
        Request $request,
        TaskRepository $taskRepo,
        EntityManagerInterface $em
    ): Response {

        $this->denyAccessUnlessGranted('ROLE_USER');

        $task = $taskRepo->find($id);

        if (!$task) {
            throw $this->createNotFoundException('Tâche introuvable (id = ' . $id . ').');
        }

        $isAdmin   = $this->isGranted('ROLE_ADMIN');
        $currentId = $this->getUser()?->getId();

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


            // ─── Modifier les champs ──

            $task->setMovement($newMovement);

            if ($isAdmin) {

                $task->setTitle($newTitle);
                $task->setPriority($newPriority);
                $task->setDueDate($newDueDate !== '' ? $newDueDate : null);

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

        return $this->render('modify_task/edit.html.twig', [
            'task' => $task,
        ]);
    }
}
