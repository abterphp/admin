<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Grid\Factory;

use AbterPhp\Admin\Grid\Factory\Table\BodyFactory;
use AbterPhp\Admin\Grid\Factory\Table\HeaderFactory;
use AbterPhp\Framework\Grid\Component\Body;
use AbterPhp\Framework\Grid\Component\Header;
use AbterPhp\Framework\Grid\Table\Table;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TableFactoryTest extends TestCase
{
    public function testCreateCallsHeaderAndBodyFactories()
    {
        /** @var Header|MockObject $headerMock */
        $headerMock = $this->createMock(Header::class);

        /** @var Body|MockObject $headerMock */
        $bodyMock = $this->createMock(Body::class);

        /** @var HeaderFactory|MockObject $headerFactoryMock */
        $headerFactoryMock = $this->createMock(HeaderFactory::class);
        $headerFactoryMock->expects($this->once())->method('create')->willReturn($headerMock);

        /** @var BodyFactory|MockObject $bodyFactoryMock */
        $bodyFactoryMock = $this->createMock(BodyFactory::class);
        $bodyFactoryMock->expects($this->once())->method('create')->willReturn($bodyMock);

        $sut = new TableFactory($headerFactoryMock, $bodyFactoryMock);

        $table = $sut->create([], null, [], '');

        $this->assertInstanceOf(Table::class, $table);
    }
}
