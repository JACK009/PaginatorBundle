<?php

namespace Jack009\Tests\Service;

use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Jack009\PaginatorBundle\Service\PaginatorService;
use Jack009\PaginatorBundle\DTO\Paginator as PaginatorDTO;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PaginatorServiceTest extends TestCase
{
    public function testGetPaginationHappyPath(): void
    {
        /** @var Query|MockObject $queryMock */
        $queryMock = $this->createMock(Query::class);

        // Expect setMaxResults and setFirstResult with limit=10 and offset=(2-1)*10=10
        $queryMock->expects($this->once())->method('setMaxResults')->with(10)->willReturnSelf();
        $queryMock->expects($this->once())->method('setFirstResult')->with(10)->willReturnSelf();

        // Create a service that returns a paginator stub with 25 total results and 10 items on the current page
        $service = new class extends PaginatorService {
            protected function createPaginator(Query $query): DoctrinePaginator
            {
                $items = range(1, 10);
                $total = 25;

                return new class($query, $items, $total) extends DoctrinePaginator {
                    private array $itemsArr;
                    private int $totalCount;

                    public function __construct(Query $q, array $items, int $total)
                    {
                        parent::__construct($q);
                        $this->itemsArr = $items;
                        $this->totalCount = $total;
                    }

                    public function count(): int
                    {
                        return $this->totalCount;
                    }

                    public function getIterator(): \ArrayIterator
                    {
                        return new \ArrayIterator($this->itemsArr);
                    }
                };
            }
        };

        $dto = $service->getPagination($queryMock, 'route', 2, 10);

        $this->assertInstanceOf(PaginatorDTO::class, $dto);
        $this->assertSame(10, $dto->getPageSize());
        $this->assertSame(2, $dto->getCurrentPage());
        $this->assertSame(3, $dto->getMaxPages()); // ceil(25/10) -> 3
        $this->assertSame(25, $dto->getTotalResults());
        $this->assertCount(10, $dto->getItems());
    }

    public function testLimitIsCappedToMaxLimit(): void
    {
        /** @var Query|MockObject $queryMock */
        $queryMock = $this->createMock(Query::class);

        // Expect setMaxResults to be called with cap = 100 and offset accordingly (page 1 -> offset 0)
        $queryMock->expects($this->once())->method('setMaxResults')->with(100)->willReturnSelf();
        $queryMock->expects($this->once())->method('setFirstResult')->with(0)->willReturnSelf();

        $service = new class(10, 100) extends PaginatorService {
            protected function createPaginator(Query $query): DoctrinePaginator
            {
                $items = range(1, 100);
                $total = 250;

                return new class($query, $items, $total) extends DoctrinePaginator {
                    private array $itemsArr;
                    private int $totalCount;

                    public function __construct(Query $q, array $items, int $total)
                    {
                        parent::__construct($q);
                        $this->itemsArr = $items;
                        $this->totalCount = $total;
                    }

                    public function count(): int
                    {
                        return $this->totalCount;
                    }

                    public function getIterator(): \ArrayIterator
                    {
                        return new \ArrayIterator($this->itemsArr);
                    }
                };
            }
        };

        $dto = $service->getPagination($queryMock, 'route', 1, 1000);

        $this->assertSame(100, $dto->getPageSize());
        $this->assertSame(1, $dto->getCurrentPage());
        $this->assertSame(3, $dto->getMaxPages()); // ceil(250/100) -> 3
        $this->assertSame(250, $dto->getTotalResults());
        $this->assertCount(100, $dto->getItems());
    }

    public function testPageZeroProducesNegativeOffsetBehavior(): void
    {
        /** @var Query|MockObject $queryMock */
        $queryMock = $this->createMock(Query::class);

        // When page=0 and limit=10, offset = (0-1)*10 = -10. Expect setFirstResult(-10).
        $queryMock->expects($this->once())->method('setMaxResults')->with(10)->willReturnSelf();
        $queryMock->expects($this->once())->method('setFirstResult')->with(-10)->willReturnSelf();

        $service = new class extends PaginatorService {
            protected function createPaginator(Query $query): DoctrinePaginator
            {
                $items = [];
                $total = 0;

                return new class($query, $items, $total) extends DoctrinePaginator {
                    private array $itemsArr;
                    private int $totalCount;

                    public function __construct(Query $q, array $items, int $total)
                    {
                        parent::__construct($q);
                        $this->itemsArr = $items;
                        $this->totalCount = $total;
                    }

                    public function count(): int
                    {
                        return $this->totalCount;
                    }

                    public function getIterator(): \ArrayIterator
                    {
                        return new \ArrayIterator($this->itemsArr);
                    }
                };
            }
        };

        $dto = $service->getPagination($queryMock, 'route', 0, 10);

        $this->assertSame(10, $dto->getPageSize());
        $this->assertSame(0, $dto->getCurrentPage());
        $this->assertSame(0, $dto->getMaxPages());
        $this->assertSame(0, $dto->getTotalResults());
        $this->assertCount(0, $dto->getItems());
    }
}

