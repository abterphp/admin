<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Grid\Factory;

use AbterPhp\Framework\Grid\Pagination\Options;
use AbterPhp\Framework\Grid\Pagination\Pagination;
use PHPUnit\Framework\TestCase;

class PaginationFactoryTest extends TestCase
{
    /** @var PaginationFactory */
    protected $sut;

    public function setUp(): void
    {
        $options = new Options(10, [5, 10, 25, 50], 3);

        $this->sut = new PaginationFactory($options);

        parent::setUp();
    }

    public function testCreate()
    {
        $params = [];

        $actualResult = $this->sut->create($params, '/foo?');

        $this->assertInstanceOf(Pagination::class, $actualResult);
    }
}
