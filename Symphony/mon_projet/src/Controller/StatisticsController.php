<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use App\Repository\GroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatisticsController extends AbstractController
{
    #[Route('/statistics', name: 'app_statistics')]
    public function index(TaskRepository $taskRepository, GroupRepository $groupRepository): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $role = $user->getRole();
        $userGroup = $user->getGroup();
        $userGroupId = $userGroup?->getId();
        $userGroupName = $userGroup?->getName() ?? '';

        // Charger les tâches selon le rôle
        if ($role === 'Admin' || $userGroupId === null) {
            $taches = $taskRepository->findAll();
        } else {
            $taches = $taskRepository->findBy(['groupId' => $userGroupId]);
        }

        // Formater les tâches pour le JS
        $tachesArray = [];
        foreach ($taches as $t) {
            $groupName = '';
            if ($t->getGroupId()) {
                $group = $groupRepository->find($t->getGroupId());
                $groupName = $group?->getName() ?? '';
            }

            $tachesArray[] = [
                'id'         => $t->getId(),
                'titre'      => $t->getTitle(),
                'priority'   => $t->getPriority(),
                'movement'   => $t->getMovement(),
                'faite'      => $t->getMovement() === 'allegro',
                'date'       => $t->getDueDate() ?? '',
                'due_date'   => $t->getDueDate() ?? '',
                'group_id'   => $t->getGroupId(),
                'group_name' => $groupName,
            ];
        }

        return $this->render('statistics/index.html.twig', [
            'taches_json'     => json_encode($tachesArray),
            'role'            => $role,
            'user_group_id'   => $userGroupId,
            'user_group_name' => $userGroupName,
            'username'        => $user->getUsername(),
        ]);
    }
}