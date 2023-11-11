<?php

declare(strict_types=1);

namespace Core\Application\Traits;

use Doctrine\ORM\QueryBuilder;

trait QueryBuilderTrait
{

    private object $searchParams;

    /**
     * @param QueryBuilder $query
     * @param array $criteria
     * @return QueryBuilder
     */
    private function getQueryConditions(QueryBuilder $query, array $criteria): QueryBuilder
    {
        return $query;
    }
}