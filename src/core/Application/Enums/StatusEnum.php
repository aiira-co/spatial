<?php
declare(strict_types=1);
namespace Core\Application\Enums;

enum StatusEnum:int {
    case UNPUBLISHED = 1;
    case PUBLISHED = 2;
    case TRASHED = 3;
    case UNLISTED = 4;
}