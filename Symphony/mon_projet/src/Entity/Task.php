<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ORM\Table(name: 'task')]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(type: 'string', length: 10)]
    private string $priority;

    #[ORM\Column(type: 'string', length: 20)]
    private string $movement;

    #[ORM\Column(type: 'json')]
    private array $tags = [];

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $dueDate = null;

    #[ORM\Column(length: 30)]
    private ?string $status = 'in progress';

    // ─────────────────────────────────────────────────────────────
    //  COLONNE group_id — conservée pour compatibilité avec le projet PHP/JS.
    //
    //  'name: group_id' → dit à Doctrine que la colonne SQL s'appelle
    //  'group_id' (snake_case) même si la propriété PHP s'appelle
    //  '$groupId' (camelCase).
    //  Sans ce 'name:', Doctrine cherche une colonne 'group_id' mais
    //  ne reconnaît pas qu'elle correspond à '$groupId' → erreur de sync.
    //
    //  nullable: true → la colonne peut être NULL (tâches sans groupe).
    //  Symfony ne l'utilise pas dans sa logique — juste déclarée pour
    //  que Doctrine soit en sync avec la BDD existante.
    // ─────────────────────────────────────────────────────────────
    #[ORM\Column(name: 'group_id', type: 'integer', nullable: true)]
    private ?int $groupId = null;

    // ─────────────────────────────────────────────────────────────
    //  RELATION ManyToOne → User
    //
    //  Une tâche appartient à UN seul utilisateur.
    //  JoinColumn name: 'user_id' → colonne FK dans la table task.
    //  onDelete: 'CASCADE' → si le user est supprimé, ses tâches aussi.
    //  Correspond exactement à la contrainte fk_task_user en BDD.
    // ─────────────────────────────────────────────────────────────
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    // ── GETTERS & SETTERS ────────────────────────────────────────

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getPriority(): string
    {
        return $this->priority;
    }
    public function setPriority(string $priority): self
    {
        $this->priority = $priority;
        return $this;
    }

    public function getMovement(): string
    {
        return $this->movement;
    }
    public function setMovement(string $movement): self
    {
        $this->movement = $movement;
        return $this;
    }

    public function getTags(): array
    {
        return $this->tags;
    }
    public function setTags(array $tags): self
    {
        $this->tags = $tags;
        return $this;
    }

    public function getDueDate(): ?string
    {
        return $this->dueDate;
    }
    public function setDueDate(?string $dueDate): self
    {
        $this->dueDate = $dueDate;
        return $this;
    }

    // group_id → utilisé par le projet PHP/JS, ignoré par Symfony.
    public function getGroupId(): ?int
    {
        return $this->groupId;
    }
    public function setGroupId(?int $groupId): self
    {
        $this->groupId = $groupId;
        return $this;
    }

    // User propriétaire de la tâche.
    public function getUser(): User
    {
        return $this->user;
    }
    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function __toString(): string
    {
        return $this->title;
    }
    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }
}
