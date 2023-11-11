<?php

declare(strict_types=1);

namespace Core\Domain\Identity;

use Core\Application\Enums\SignatureTypeEnum;
use DateTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;


#[\Doctrine\ORM\Mapping\Entity]
#[Table(name: "signature")]
class Signature
{
    #[Id, Column, GeneratedValue]
    public int $id;

    /**
     * Many claims have one person. This is the owning side.
     */
    #[ManyToOne(inversedBy: 'signatures')]
    #[JoinColumn(name: 'person_id', referencedColumnName: "id")]
    public Person $person;

    #[Column]
    public string $hashed;

    /**
     * 66 - password, 67 - pin, 68 - fingerprint, 69 -faceId
     */
    #[Column(type: 'int', enumType: SignatureTypeEnum::class)]
    public SignatureTypeEnum $type;

    #[Column(options: ["default" => "CURRENT_TIMESTAMP"])]
    public DateTime $created;


    /**
     * @param string $hashed
     * @return bool
     */
    public function authenticate(string $hashed): bool
    {
        return password_verify($hashed, $this->hashed);
        // && !$this->hasActiveBans();
    }

    /**
     * @param string $hashed
     * @return self
     */
    public function setHashed(string $hashed): self
    {
        $this->hashed = password_hash($hashed, PASSWORD_DEFAULT);
        return $this;
    }

}