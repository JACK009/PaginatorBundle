<?php

namespace Jack009\PaginatorBundle\DTO;

class Paginator
{
    public function __construct(
        public ?int $maxPages = null,
        public ?int $currentPage = null,
        public ?int $pageSize = null,
        public ?int $totalResults = null,
        public ?string $routeName = null,
        public array $routeParameters = [],
        public string $pageParameter = 'page',
        public array $items = []
    )
    {}
}
