<?php

namespace App\PaginatorBundle\Service;

use App\PaginatorBundle\DTO\Paginator;
use Doctrine\ORM\Query;

interface IPaginatorService
{
    public function getPagination(Query $queryBuilder, string $routeName, int $page = 1, ?int $limit = null, array $routeParameters = [], string $pageParameter = 'page'): Paginator;
}
