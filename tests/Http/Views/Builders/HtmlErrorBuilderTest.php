<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Views\Builders;

use Opulence\Views\IView;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class HtmlErrorBuilderTest extends TestCase
{
    /** @var HtmlErrorBuilder - System Under Test */
    protected $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new HtmlErrorBuilder();
    }

    public function testBuildWorks()
    {
        /** @var IView|MockObject $viewMock */
        $viewMock = $this->createMock(IView::class);

        $actualResult = $this->sut->build($viewMock);

        $this->assertSame($viewMock, $actualResult);
    }
}
