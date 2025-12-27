<?php

namespace Jack009\PaginatorBundle\Service;

use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

class PaginatorService implements IPaginatorService
{
    protected int $maxResults;
    protected int $maxLimit;

    public function __construct(int $maxResults = 10, int $maxLimit = 100)
    {
        $this->maxResults = $maxResults;
        $this->maxLimit = $maxLimit;
    }

    public function getPagination(Query $queryBuilder, string $routeName, int $page = 1, ?int $limit = null, array $routeParameters = [], string $pageParameter = 'page'): \Jack009\PaginatorBundle\DTO\Paginator
    {
        $limit = $limit ?? $this->maxResults;
        $limit = min($limit, $this->maxLimit);
        $offset = ($page - 1) * $limit;

        $queryBuilder->setMaxResults($limit)
            ->setFirstResult($offset);

        // Use factory method so tests can inject a fake paginator
        $paginator = $this->createPaginator($queryBuilder);
        $totalResults = count($paginator);

        return new \Jack009\PaginatorBundle\DTO\Paginator(
            (int) ceil($totalResults / $limit),
            $page,
            $limit,
            $totalResults,
            $routeName,
            $routeParameters,
            $pageParameter,
            iterator_to_array($paginator)
        );
    }

    // Protected factory for creating the Doctrine Paginator; override in tests to inject a stub
    protected function createPaginator(Query $query): DoctrinePaginator
    {
        return new DoctrinePaginator($query);
    }
}
