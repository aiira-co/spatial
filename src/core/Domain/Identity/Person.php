<?php

declare(strict_types=1);

namespace Core\Domain\Identity;


use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use \DateTime;

#[Entity]
#[Table(name: "person")]
class Person
{
    #[Id, Column, GeneratedValue]
    public int $id;

    #[Column(name: 'person_id', nullable: true)]
    public ?string $personId;

    #[Column(nullable: true)]
    public ?string $image;

    #[Column(nullable: true)]
    public ?string $cover;

    #[Column(length: 32)]
    public string $username;

    #[Column(length: 32)]
    public string $tagline;

    #[Column(length: 512, nullable: true)]
    public ?string $bio;

    #[Column(length: 32, nullable: true)]
    public ?string $surname;

    #[Column(length: 32, nullable: true)]
    public ?string $othername;

    #[ManyToOne]
    #[JoinColumn(name: 'gender_id', referencedColumnName: "id")]
    public Groups $gender;

    #[ManyToOne]
    #[JoinColumn(name: 'provider_id', referencedColumnName: "id")]
    public ?Groups $provider;

    #[Column(name: 'birth_date', nullable: true)]
    public ?DateTime $birthdate;

    #[Column(length: 64)]
    public string $email;

    #[Column(name: 'phone_one', length: 15, nullable: true)]
    public ?string $phoneOne;

    #[Column(name: 'phone_two', length: 15, nullable: true)]
    public ?string $phoneTwo;

    #[Column(name: 'email_verified')]
    public bool $emailVerified;

    #[Column(name: 'two_way_auth', nullable: true)]
    public ?bool $twoWayAuth;

    #[Column]
    public bool $activated;

    #[Column(name: 'lockout_enabled')]
    public bool $lockoutEnabled;

    #[Column(name: 'lockout_end', nullable: true)]
    public ?DateTime $lockoutEnd;

    #[Column(name: 'access_failed_count')]
    public int $accessFailedCount;

    #[Column(name: 'phone_verified')]
    public bool $phoneVerified;

    #[Column(options: ["default" => "CURRENT_TIMESTAMP"])]
    public DateTime $created;

    #[Column(length: 32)]
    public string $city;

    #[Column(length: 32)]
    public string $country;

    #[Column(length: 32)]
    public string $timezone;

    #[Column(length: 32)]
    public string $language;

    #[ManyToOne]
    #[JoinColumn(name: 'account_type_id', referencedColumnName: "id")]
    public Groups $accountType;
}
