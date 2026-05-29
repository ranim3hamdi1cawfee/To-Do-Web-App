<?php

namespace App\Repository;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TaskRepository extends ServiceEntityRepository
{
    // Le constructeur reçoit le ManagerRegistry par injection de dépendances.
    // parent::__construct() lie ce repository à l'entité Task.
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    // ─────────────────────────────────────────────────────────────
    //  findByUser() — récupère TOUTES les tâches d'un utilisateur.
    //
    //  Équivalent SQL : SELECT * FROM task WHERE user_id = :id ORDER BY id DESC
    //
    //  C'est le filtre principal : chaque user ne voit QUE ses tâches.
    //  Équivalent du filtre group_id dans l'ancienne api.php.
    // ─────────────────────────────────────────────────────────────
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('t')
            // 't.user' → propriété $user de l'entité Task (la relation ManyToOne).
            // Doctrine traduit ça en WHERE user_id = :user automatiquement.
            ->andWhere('t.user = :user')
            ->setParameter('user', $user)
            ->orderBy('t.id', 'DESC') // Tâches récentes en premier.
            ->getQuery()
            ->getResult();
    }

    // ─────────────────────────────────────────────────────────────
    //  findByUserAndMovement() — filtre par user ET par mouvement (statut).
    //
    //  Équivalent SQL : SELECT * FROM task WHERE user_id = :id AND movement = :movement
    //
    //  Utile si on veut filtrer les tâches d'un user par statut.
    // ─────────────────────────────────────────────────────────────
    public function findByUserAndMovement(User $user, string $movement): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.user = :user')
            ->andWhere('t.movement = :movement')
            ->setParameter('user', $user)
            ->setParameter('movement', $movement)
            ->orderBy('t.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
    public function findByPriority(?string $priority): array
    {
        if (!$priority) {
            return $this->findBy([], ['id' => 'DESC']);
        }

        return $this->createQueryBuilder('t')
            ->andWhere('t.priority = :priority')
            ->setParameter('priority', $priority)
            ->orderBy('t.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function countActiveTasks(): int
    {
        return $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->where('t.status != :done')
            ->setParameter('done', 'done')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countUrgentTasks(): int
    {
        return $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->where('t.priority = :urgent')
            ->andWhere('t.status != :done')
            ->setParameter('urgent', 'high')
            ->getQuery()
            ->getSingleScalarResult();
    }
}