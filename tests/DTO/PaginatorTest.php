<?php

namespace Jack009\Tests\DTO;

use Jack009\PaginatorBundle\DTO\Paginator;
use PHPUnit\Framework\TestCase;

class PaginatorTest extends TestCase
{
    public function testDefaults(): void
    {
        $p = new Paginator();

        $this->assertNull($p->getMaxPages());
        $this->assertNull($p->getCurrentPage());
        $this->assertNull($p->getPageSize());
        $this->assertNull($p->getTotalResults());
        $this->assertNull($p->getRouteName());
        $this->assertSame([], $p->getRouteParameters());
        $this->assertSame('page', $p->getPageParameter());
        $this->assertSame([], $p->getItems());
    }

    public function testConstructorValues(): void
    {
        $items = ['a', 'b'];
        $p = new Paginator(5, 2, 10, 45, 'route_name', ['foo' => 'bar'], 'p', $items);

        $this->assertSame(5, $p->getMaxPages());
        $this->assertSame(2, $p->getCurrentPage());
        $this->assertSame(10, $p->getPageSize());
        $this->assertSame(45, $p->getTotalResults());
        $this->assertSame('route_name', $p->getRouteName());
        $this->assertSame(['foo' => 'bar'], $p->getRouteParameters());
        $this->assertSame('p', $p->getPageParameter());
        $this->assertSame($items, $p->getItems());
    }
}

