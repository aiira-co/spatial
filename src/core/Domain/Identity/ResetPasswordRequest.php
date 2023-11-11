<?php

namespace Core\Domain\Identity;

use DateTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table(name: "reset_password_request")]
class ResetPasswordRequest
{
    #[Id, Column, GeneratedValue]
    public int $id;

    #[ManyToOne]
    #[JoinColumn(name: 'person_id', referencedColumnName: "id")]
    public Person $person;

    #[Column]
    public string $token;

    #[Column]
    public string $requestedIp;

    #[Column(nullable: true)]
    public ?string $resetIp;

    #[Column]
    public bool $isResetDone;

    #[Column(options: ["default" => "CURRENT_TIMESTAMP"])]
    public DateTime $created;

    #[Column(options: ["default" => "CURRENT_TIMESTAMP"])]
    public DateTime $modified;

    public function isExpired(): bool
    {
        return $this->created->diff(new DateTime())->days > 1;
    }

}