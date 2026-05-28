<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['username'], message: 'Username already taken.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 50, unique: true)]
    private string $username;

    #[ORM\Column(type: 'string', length: 255)]
    private string $password;

    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $birthday;

    #[ORM\Column(type: 'string', columnDefinition: "ENUM('Admin', 'Regular') NOT NULL DEFAULT 'Regular'")]
    private string $role = 'Regular';

    #[ORM\ManyToOne(targetEntity: Group::class)]
    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Group $group = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['default' => 'default_avatar.png'])]
    private ?string $profileImage = 'default_avatar.png';

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['default' => 'Stay focused.'])]
    private ?string $motto = 'Stay focused.';

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $currentStreak = 0;

    #[ORM\Column(type: 'datetime', columnDefinition: "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP")]
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }

    public function getUsername(): string { return $this->username; }
    public function setUsername(string $username): self { $this->username = $username; return $this; }

    public function getUserIdentifier(): string { return $this->username; }

    public function getPassword(): string { return $this->password; }
    public function setPassword(string $password): self { $this->password = $password; return $this; }

    public function getBirthday(): \DateTimeInterface { return $this->birthday; }
    public function setBirthday(\DateTimeInterface $birthday): self { $this->birthday = $birthday; return $this; }

    public function getRole(): string { return $this->role; }
    public function setRole(string $role): self { $this->role = $role; return $this; }

    public function getRoles(): array
    {
        return $this->role === 'Admin' ? ['ROLE_ADMIN', 'ROLE_USER'] : ['ROLE_USER'];
    }

    public function getGroup(): ?Group { return $this->group; }
    public function setGroup(?Group $group): self { $this->group = $group; return $this; }

    public function getProfileImage(): ?string { return $this->profileImage; }
    public function setProfileImage(?string $profileImage): self { $this->profileImage = $profileImage; return $this; }

    public function getMotto(): ?string { return $this->motto; }
    public function setMotto(?string $motto): self { $this->motto = $motto; return $this; }

    public function getCurrentStreak(): int { return $this->currentStreak; }
    public function setCurrentStreak(int $currentStreak): self { $this->currentStreak = $currentStreak; return $this; }

    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $createdAt): self { $this->createdAt = $createdAt; return $this; }

    public function eraseCredentials(): void {}
}