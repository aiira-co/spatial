<?php

declare(strict_types=1);

namespace Core\Domain\Identity;

use Core\Application\Enums\AccountTypeEnum;
use Core\Application\Enums\GenderEnum;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use JetBrains\PhpStorm\Pure;

// src/core/domain/identity/User.php

#[\Doctrine\ORM\Mapping\Entity]
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

    #[Column(type: 'int', enumType: GenderEnum::class)]
    public GenderEnum $gender;

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

    #[Column(type: 'int', enumType: AccountTypeEnum::class)]
    public AccountTypeEnum $accountType;

    #[Column(name: 'is_verified')]
    public ?bool $isVerified;


    #[OneToMany(mappedBy: 'person', targetEntity: Signature::class)]
    public Collection $signatures;


    /**
     * Constructor
     */
    #[Pure] public function __construct()
    {
        $this->signatures = new ArrayCollection();
    }

}
