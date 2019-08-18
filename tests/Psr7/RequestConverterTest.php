<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Psr7;

use Opulence\Http\Requests\Request;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class RequestConverterTest extends TestCase
{
    /** @var RequestConverter - System Under Test */
    protected $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new RequestConverter();
    }

    public function testToPsr()
    {
        $opulenceRequest = new Request([], [], [], [], [], [], null);

        $psr7Request = $this->sut->toPsr($opulenceRequest);

        $this->assertInstanceOf(ServerRequestInterface::class, $psr7Request);
    }

    public function testToPsrWithJsonBody()
    {
        $rawBody = json_encode(['foo' => 'bar']);

        $server = [
            'CONTENT_TYPE' => ['application/json'],
        ];

        $opulenceRequest = new Request([], [], [], $server, [], [], $rawBody);

        $psr7Request = $this->sut->toPsr($opulenceRequest);

        $this->assertInstanceOf(ServerRequestInterface::class, $psr7Request);
    }
}
