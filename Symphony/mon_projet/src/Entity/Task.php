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

    #[ORM\Column(type: 'json', columnDefinition: "LONGTEXT NOT NULL CHECK (json_valid(`tags`))")]
    private array $tags = [];

    #[ORM\Column(type: 'string', length: 20, nullable: true, columnDefinition: "VARCHAR(20) DEFAULT NULL")]
    private ?string $dueDate = null;

    #[ORM\Column(type: 'integer', nullable: true, name: 'group_id')]
    private ?int $groupId = null;

    #[ORM\Column(type: 'integer', nullable: true, name: 'created_by')]
    private ?int $createdBy = null;

    public function getId(): ?int { return $this->id; }

    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): self { $this->title = $title; return $this; }

    public function getPriority(): string { return $this->priority; }
    public function setPriority(string $priority): self { $this->priority = $priority; return $this; }

    public function getMovement(): string { return $this->movement; }
    public function setMovement(string $movement): self { $this->movement = $movement; return $this; }

    public function getTags(): array { return $this->tags; }
    public function setTags(array $tags): self { $this->tags = $tags; return $this; }

    public function getDueDate(): ?string { return $this->dueDate; }
    public function setDueDate(?string $dueDate): self { $this->dueDate = $dueDate; return $this; }

    public function getGroupId(): ?int { return $this->groupId; }
    public function setGroupId(?int $groupId): self { $this->groupId = $groupId; return $this; }

    public function getCreatedBy(): ?int { return $this->createdBy; }
    public function setCreatedBy(?int $createdBy): self { $this->createdBy = $createdBy; return $this; }

    public function __toString(): string { return $this->title; }
}