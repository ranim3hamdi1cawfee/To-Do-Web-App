<?php

declare(strict_types=1);

namespace Doctrine\Bundle\DoctrineBundle\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\VarExporter\LazyObjectInterface;

interface LazyObjectEntityManagerInterface extends LazyObjectInterface, EntityManagerInterface
{
}
