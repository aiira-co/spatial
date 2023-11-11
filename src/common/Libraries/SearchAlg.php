<?php

declare(strict_types=1);

namespace Common\Libraries;

use Doctrine\ORM\QueryBuilder;

use function str_replace;
use function trim;

class SearchAlg
{


    private $searchQueryArr = [];

    /**
     * Search should consider key words like
     * and, or, nor for, by
     *
     * @param string $searchQuery
     */
    public function __construct(?string $searchQuery)
    {
        // remove trailing whitespace
        if ($searchQuery === null || trim($searchQuery) === '') {
            $this->searchQueryArr = ['%'];
            // return;
        } else {
            $searchQuery = trim($searchQuery);
            // keywords like and, or cold be considered
            // keywords : could be considered later
            $searchQuery = str_replace(' ', ' %', '%' . $searchQuery . '%');
            $this->searchQueryArr = explode(' ', $searchQuery);
        }
    }

    public function getSearchQuery()
    {
        return $this->searchQueryArr;
    }


    public function genSearchParams(QueryBuilder $query, ?array $searchFields = null): object
    {
        $parameters = [];
        $searchFields = $searchFields ?? ['t.title', 't.tag'];

        $searchCount = count($this->searchQueryArr);
        $searchStatement = [];

        if ($searchCount === 1) {
            $parameters = [
                'search' => $this->searchQueryArr[0],
            ];
            foreach ($searchFields as $key) {
                $searchStatement[] = $query->expr()->like('lower(' . $key . ')', 'lower(:search)');
            }
        } else {
            foreach ($this->searchQueryArr as $i => $iValue) {
                $index = 'search_' . $i;
                $parameters[$index] = $iValue;
                foreach ($searchFields as $key) {
                    $searchStatement[] = $query->expr()->like('lower(' . $key . ')', 'lower(:' . $index . ')');
                }
                // $searchStatement[] =  $query->expr()->like('lower(t.tag)', 'lower(:' . $index . ')');
            }
        }

        return (object)[
            'params' => $parameters,
            'search' => $searchStatement
        ];
    }

    /**
     * Search the entire thing
     *
     * @param string $query
     * @return string
     */
    public function simpleSearch(string $query): string
    {
        return '%' . trim($query) . '%';
    }

    /**
     * Search word by word
     *
     * @param string $query
     * @return string
     */
    public function wordSearch(string $query): string
    {
        $q = trim($query);
        $q = str_replace(' ', '%', $q);
        return '%' . trim($q) . '%';
    }
}
