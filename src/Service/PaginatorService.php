<?php

namespace Jack009\PaginatorBundle\Service;

use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;

class PaginatorService implements IPaginatorService
{
    protected $maxResults = 3;
    protected $maxLimit = 100;

    public function getPagination(Query $queryBuilder, string $routeName, int $page = 1, ?int $limit = null, array $routeParameters = [], string $pageParameter = 'page'): \Jack009\PaginatorBundle\DTO\Paginator
    {
        $limit = $limit ?? $this->maxResults;
        $limit = min($limit, $this->maxLimit);
        $offset = ($page - 1) * $limit;

        $queryBuilder->setMaxResults($limit)
            ->setFirstResult($offset);

        $paginator = new Paginator($queryBuilder);
        $totalResults = count($paginator);

        return new \Jack009\PaginatorBundle\DTO\Paginator(
            ceil($totalResults / $limit),
            $page,
            $limit,
            $totalResults,
            $routeName,
            $routeParameters,
            $pageParameter,
            iterator_to_array($paginator)
        );
    }
}
