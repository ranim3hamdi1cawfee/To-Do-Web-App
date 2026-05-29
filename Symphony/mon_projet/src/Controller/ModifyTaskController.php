<?php
namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ModifyTaskController extends AbstractController
{
    #[Route('/tasks/modify/{id}', name: 'task_modify', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function modify(
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

        $isAdmin = $this->isGranted('ROLE_ADMIN');

        if ($request->isMethod('POST')) {

            $newStatus = $request->request->get('status', 'in progress');
            $allowedStatuses = ['todo', 'in progress', 'done'];

            if (in_array($newStatus, $allowedStatuses, true)) {
                $task->setStatus($newStatus);
            }

            if ($isAdmin) {

                $newTitle    = trim($request->request->get('title', ''));
                $newPriority = $request->request->get('priority', 'medium');
                $newMovement = $request->request->get('movement', 'andante');
                $newDueDate  = trim($request->request->get('due_date', ''));
                $tagsRaw     = trim($request->request->get('tags', ''));


                // Validation simple : le titre est obligatoire
                if ($newTitle === '') {
                    $this->addFlash('error', 'Le titre est obligatoire.');
                    return $this->render('modify_task/edit.html.twig', [
                        'task' => $task,
                    ]);
                }


                // Modifier les autres champs
                $task->setTitle($newTitle);
                $task->setPriority($newPriority);
                $task->setMovement($newMovement);
                $task->setDueDate($newDueDate !== '' ? $newDueDate : null);


                $tagsArray = $tagsRaw !== ''
                    ? array_map('trim', explode(',', $tagsRaw))
                    : [];
                $task->setTags($tagsArray);
            }
            $em->flush();

            $this->addFlash('success', 'Tâche "' . $task->getTitle() . '" modifiée !');

            return $this->redirectToRoute('task_list');
        }
        return $this->render('modify_task/edit.html.twig', [
            'task' => $task,
        ]);
    }
}