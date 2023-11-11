<?php
declare(strict_types=1);
namespace Core\Application\Enums;

enum AccountTypeEnum:int {
    case CLIENT = 1;
    case SELLER = 2;
    case ADMIN = 3;
}