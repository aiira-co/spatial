<?php

declare(strict_types=1);

namespace Core\Domain\Identity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

// src/core/domain/identity/Groups.php


#[\Doctrine\ORM\Mapping\Entity]
#[Table(name: "groups")]
class Groups
{
    #[Id, Column, GeneratedValue]
    public int $id;

    #[Column(length: 32)]
    public string $name;

    #[Column(length: 32)]
    public string $alias;

    #[Column(length: 120)]
    public string $description;

    #[Column]
    public DateTime $created;

    #[ManyToOne]
    #[JoinColumn(name: 'status_id', referencedColumnName: "id", nullable: true)]
    public ?Groups $status;

    #[ManyToOne]
    #[JoinColumn(name: 'groups_id', referencedColumnName: "id", nullable: true)]
    public ?Groups $groups;

    #[ManyToOne]
    #[JoinColumn(name: 'grouptype_id', referencedColumnName: "id")]
    public GroupType $groupType;

    #[Column(length: 32)]
    public string $param;
}
