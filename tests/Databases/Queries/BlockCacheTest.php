<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Databases\Queries;

use AbterPhp\Framework\TestCase\Database\QueryTestCase;
use AbterPhp\Framework\TestDouble\Database\MockStatementFactory;

class BlockCacheTest extends QueryTestCase
{
    /** @var BlockCache - System Under Test */
    protected $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new BlockCache($this->connectionPoolMock);
    }

    public function testHasAnyChangedSinceReturnsFalseIfNothingHasChanged()
    {
        $identifiers = ['foo', 'bar'];
        $cacheTime   = 'baz';

        $sql          = 'SELECT COUNT(*) AS count FROM blocks LEFT JOIN block_layouts AS layouts ON layouts.id = blocks.layout_id WHERE (blocks.deleted = 0) AND (blocks.identifier IN (?,?)) AND (blocks.updated_at > ? OR layouts.updated_at > ?)'; // phpcs:ignore
        $valuesToBind = [
            [$identifiers[0], \PDO::PARAM_STR],
            [$identifiers[1], \PDO::PARAM_STR],
            [$cacheTime, \PDO::PARAM_STR],
            [$cacheTime, \PDO::PARAM_STR],
        ];
        $returnValue  = '0';
        $statement    = MockStatementFactory::createReadColumnStatement($this, $valuesToBind, $returnValue);
        MockStatementFactory::prepare($this, $this->readConnectionMock, $sql, $statement);

        $actualResult = $this->sut->hasAnyChangedSince($identifiers, $cacheTime);

        $this->assertFalse($actualResult);
    }

    public function testHasAnyChangedSinceReturnsTrueIfSomeBlocksHaveChanged()
    {
        $identifiers = ['foo', 'bar'];
        $cacheTime   = 'baz';

        $sql          = 'SELECT COUNT(*) AS count FROM blocks LEFT JOIN block_layouts AS layouts ON layouts.id = blocks.layout_id WHERE (blocks.deleted = 0) AND (blocks.identifier IN (?,?)) AND (blocks.updated_at > ? OR layouts.updated_at > ?)'; // phpcs:ignore
        $valuesToBind = [
            [$identifiers[0], \PDO::PARAM_STR],
            [$identifiers[1], \PDO::PARAM_STR],
            [$cacheTime, \PDO::PARAM_STR],
            [$cacheTime, \PDO::PARAM_STR],
        ];
        $returnValue  = '2';
        $statement    = MockStatementFactory::createReadColumnStatement($this, $valuesToBind, $returnValue);
        MockStatementFactory::prepare($this, $this->readConnectionMock, $sql, $statement);

        $actualResult = $this->sut->hasAnyChangedSince($identifiers, $cacheTime);

        $this->assertTrue($actualResult);
    }
}
