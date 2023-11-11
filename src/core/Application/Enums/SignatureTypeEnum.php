<?php
declare(strict_types=1);
namespace Core\Application\Enums;
//66 - password, 67 - pin, 68 - fingerprint, 69 -faceId
enum SignatureTypeEnum:int {
    case PASSWORD = 1;
    case PIN = 2;
    case FINGUREPRINT = 3;
    case FACEID = 4;
}