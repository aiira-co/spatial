<?php

declare(strict_types=1);

namespace Core\Domain\Identity;

// src/core/domain/identity/GroupType.php
use DateTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

#[\Doctrine\ORM\Mapping\Entity]
#[Table(name: "grouptype")]
class GroupType
{
    #[Id, Column, GeneratedValue]
    public int $id;

    #[Column(length: 32)]
    public string $name;

    #[Column(length: 120)]
    public string $description;

    #[Column]
    public DateTime $created;
}
