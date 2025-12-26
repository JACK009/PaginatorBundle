<?php

namespace Jack009\PaginatorBundle\Service;

use Doctrine\ORM\Query;
use Jack009\PaginatorBundle\DTO\Paginator;

interface IPaginatorService
{
    public function getPagination(Query $queryBuilder, string $routeName, int $page = 1, ?int $limit = null, array $routeParameters = [], string $pageParameter = 'page'): Paginator;
}
