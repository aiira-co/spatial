<?php

declare(strict_types=1);

namespace Core\Application\Logics\Identity\User\Queries;

use Core\Domain\Identity\Person;
use Cqured\MediatR\IRequest;
use Infrastructure\Identity\IdentityDB;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Request To be passed to Its' Handler
 * Handler can use this class's properties and methods
 * for Server Request, etc
 */
class GetUsers extends Request
{
    public $params = [];
    public $query = '';
    public $page = 1;
    public $total;
    public $limit = 20;

    public function getUsers(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $this->emIdentity = (new IdentityDB)->emIdentity;

        $parameters = [
            'search' => $criteria['search'],
            'activated' => true,
        ];

        // get all
        $query = $this->emIdentity
            ->getRepository(Person::class)
            ->createQueryBuilder('t');
        $query = $query->where('t.activated = :activated')
            ->andWhere(
                $query->expr()->orX(
                    $query->expr()->like('t.username', ':search'),
                    $query->expr()->like('t.othername', ':search'),
                    $query->expr()->like('t.surname', ':search')
                )
            )
            ->orderBy('t.id', 'DESC');

        // print_r($parameters);
        $query = $query->setParameters($parameters)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery();

        $paginator = new Paginator($query, $fetchJoinCollection = true);

        $data = [];
        $c = count($paginator);

        foreach ($paginator as $user) {
            array_push(
                $data,
                [
                    'id' => $user->getId(),
                    'image' => $user->getImage(),
                    'username' => $user->getUsername(),
                    'name' => $user->getName(),
                    'tagline' => $user->getTagline(),

                ]
            );
        }

        // print_r($data);

        return $data;
    }

    public function countAllGroups()
    {
        // get all
        $userRepository = $this->emIdentity
            ->getRepository(Groups::class);
        $groups = $userRepository->findAll();

        return [
            $groups,
        ];
    }


    /**
     * Count Entity
     *
     * @return int
     */
    public function countTotalUsers($criteria): int
    {
        $parameters = [
            'search' => '%' . $criteria['search'] . '%',
            'activated' => true,
        ];

        $query = $this->emIdentity
            ->getRepository(Person::class)
            ->createQueryBuilder('t');

        $query = $query->select('count(t.id)')
            ->where('t.activated = :activated')
            ->andWhere(
                $query->expr()->orX(
                    $query->expr()->like('t.username', ':search'),
                    $query->expr()->like('t.othername', ':search'),
                    $query->expr()->like('t.surname', ':search')
                )
            );

        return $query
            ->setParameters($parameters)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
