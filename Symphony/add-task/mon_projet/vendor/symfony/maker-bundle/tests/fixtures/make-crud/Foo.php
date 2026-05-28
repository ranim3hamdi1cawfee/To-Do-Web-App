<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class Foo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $foo = null;

    #[ORM\Column(length: 255)]
    private ?string $foo_bar = null;

    public function getId()
    {
        return $this->id;
    }

    public function getFoo(): string
    {
        return $this->foo;
    }

    public function setFoo(string $foo): self
    {
        $this->foo = $foo;

        return $this;
    }

    public function getFooBar(): string
    {
        return $this->foo_bar;
    }

    public function setFooBar(string $foo_bar): self
    {
        $this->foo_bar = $foo_bar;

        return $this;
    }
}
