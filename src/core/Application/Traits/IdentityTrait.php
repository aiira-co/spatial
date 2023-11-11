<?php

declare(strict_types=1);

namespace Core\Application\Traits;

use DI\DependencyException;
use DI\NotFoundException;
use Doctrine\DBAL\Exception;
use OpsWay\Doctrine\ORM\Swoole\EntityManager;
use Spatial\Core\App;
use Infrastructure\Resource\AppDB;

trait IdentityTrait
{
    use QueryBuilderTrait;

    public object $params;
    public ?array $appClaimParam;
    private EntityManager $emIdentity;

    /**
     */
    public function getEntityManager(): void
    {
//        $this->emIdentity = (new IdentityDB())->emIdentity;
        try {
            $this->emIdentity = App::diContainer()->get(AppDB::class)->emSuite;
            if (!$this->emIdentity->isOpen()) {
                $this->emIdentity->reopen();
//                print('connection is closed');
            }
        } catch (DependencyException|NotFoundException|Exception $e) {
            print($e->getMessage());
        }
    }
}