<?php

declare(strict_types=1);

namespace Core\Domain\MyApp;

// src/core/domain/myapp/Product.php
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[\Doctrine\ORM\Mapping\Entity]
#[Table(name: "products")]
class Product
{
    #[Id, Column, GeneratedValue]
    public int $id;

    #[Column]
    public string $name;

}
