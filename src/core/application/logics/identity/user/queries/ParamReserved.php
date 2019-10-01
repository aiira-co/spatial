<?php
declare(strict_types=1);

namespace Core\Application\Logics\Identity\Queries;

use Core\Domain\Identity\Person;
use Cqured\MediatR\IRequest;
use Infrastructure\Identity\IdentityDB;
use Spatial\Psr7\Request;

/**
 * Request To be passed to Its' Handler
 * Handler can use this class's properties and methods
 * for Server Request, etc
 */
class ParamReserved extends Request
{

    public $data = [];

    public function checkParam()
    {
        $this->emIdentity = (new IdentityDB)->emIdentity;
        
        $user = $this->emIdentity
            ->getRepository(Person::class)
            ->findOneBy([$this->data['field'] => trim($this->data['value'])]);
        return $user ? true : false;
    }
}
