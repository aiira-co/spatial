<?php

declare(strict_types=1);

namespace Core\Application\Logics\Identity\User\Queries;

use Common\Libraries\SearchAlg;
use Core\Application\Traits\IdentityTrait;
use Core\Domain\Identity\Person;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Spatial\Psr7\Request;

/**
 * Request To be passed to Its' Handler
 * Handler can use this class's properties and methods
 * for Server Request, etc
 */
class GetUsers extends Request
{
    use IdentityTrait;

    public SearchAlg $searchAlg;

    /**
     * GetEntity Query
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return array|null
     * @throws \JsonException
     */
    public function getUsers(array $criteria, array $orderBy = null, int $limit = null, int $offset = null): ?array
    {
        $query = $this->emIdentity
            ->getRepository(Person::class)
            ->createQueryBuilder('t');

        $this->searchParams = $this->searchAlg->genSearchParams($query, ['t.email', 't.username', 't.othername', 't.surname']);

        $orderByFields = '';
        foreach ($orderBy as $key) {
            $orderByFields .= ', t.' . trim($key);
        }

        $query = $this->getQueryConditions($query, $criteria)
            ->orderBy(ltrim($orderByFields, ','), 'DESC');

        $query = $query->setParameters($this->searchParams->params)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery();

        $paginator = new Paginator($query, $fetchJoinCollection = true);

        $data = [];
        $c = count($paginator);

        foreach ($paginator as $user) {
            $data[] =
                [
                    'id' => $user->id,
                    'image' => $user->image,
                    'username' => $user->username,
                    'email' => $user->email,
                    'name' => $user->othername . ' ' . $user->surname,
                    'accountType' => $user->accountType,
                    'gender' => $user->gender,
                    'tagline' => $user->tagline,
                    'isVerified' => $user->isVerified,

                ];
        }

        return $data;
    }

    /**
     * @param QueryBuilder $query
     * @param array $criteria
     * @return QueryBuilder
     */
    private
    function getQueryConditions(
        QueryBuilder $query,
        array        $criteria
    ): QueryBuilder
    {
        $this->searchParams->params['activated'] = true;

        $query = $query
            ->where('t.activated = :activated')
            ->andWhere($query->expr()->orX(...$this->searchParams->search));


        return $query;
    }


    /**
     * Count Entity
     *
     * @param array $criteria
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public
    function countTotalUsers(
        array $criteria
    ): int
    {
        $query = $this->emIdentity
            ->getRepository(Person::class)
            ->createQueryBuilder('t')
            ->select('count(t.id)');

        $total = (int)$this->getQueryConditions($query, $criteria)
            ->setParameters($this->searchParams->params)
            ->getQuery()
            ->getSingleScalarResult();

        $this->emIdentity->close();
        return $total;
    }
}
