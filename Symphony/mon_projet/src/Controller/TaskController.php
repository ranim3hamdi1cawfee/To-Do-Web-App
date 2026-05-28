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

class TaskController extends AbstractController
{
    #[Route('/tasks', name: 'task_list')]
    public function index(TaskRepository $repo): Response
    {
        return $this->render('task/index.html.twig', [
            'tasks' => $repo->findAll(),
        ]);
    }

    #[Route('/tasks/add', name: 'task_add')]
    public function add(Request $request, EntityManagerInterface $manager): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tagsRaw = $form->get('tags')->getData();
            $task->setTags(
                $tagsRaw ? array_map('trim', explode(',', $tagsRaw)) : []
            );

            $manager->persist($task);
            $manager->flush();

            $this->addFlash('success', 'Tâche "' . $task->getTitle() . '" ajoutée !');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/tasks/delete/{id}', name: 'task_delete')]
    public function delete(Task $task, EntityManagerInterface $manager): Response
    {
        $manager->remove($task);
        $manager->flush();

        $this->addFlash('success', 'Tâche supprimée.');

        return $this->redirectToRoute('task_list');
    }
}