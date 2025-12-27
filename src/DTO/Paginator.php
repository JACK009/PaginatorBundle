<?php

namespace Jack009\PaginatorBundle\DTO;

class Paginator
{
    public function __construct(
        private ?int $maxPages = null,
        private ?int $currentPage = null,
        private ?int $pageSize = null,
        private ?int $totalResults = null,
        private ?string $routeName = null,
        private array $routeParameters = [],
        private string $pageParameter = 'page',
        private array $items = []
    )
    {}

    public function getMaxPages(): ?int
    {
        return $this->maxPages;
    }

    public function getCurrentPage(): ?int
    {
        return $this->currentPage;
    }

    public function getPageSize(): ?int
    {
        return $this->pageSize;
    }

    public function getTotalResults(): ?int
    {
        return $this->totalResults;
    }

    public function getRouteName(): ?string
    {
        return $this->routeName;
    }

    public function getRouteParameters(): array
    {
        return $this->routeParameters;
    }

    public function getPageParameter(): string
    {
        return $this->pageParameter;
    }

    public function getItems(): array
    {
        return $this->items;
    }
}
