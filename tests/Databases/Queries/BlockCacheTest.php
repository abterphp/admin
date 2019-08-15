<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Databases\Queries;

use AbterPhp\Framework\TestCase\Database\QueryTestCase;

class BlockCacheTest extends QueryTestCase
{
    /** @var BlockCache - System Under Test */
    protected $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new BlockCache($this->connectionPoolMock);
    }

    public function testTestIncomplete()
    {
        $this->markTestIncomplete();
    }
}
