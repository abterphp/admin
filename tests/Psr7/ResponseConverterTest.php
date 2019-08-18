<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Psr7;

use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ResponseConverterTest extends TestCase
{
    /** @var ResponseConverter - System Under Test */
    protected $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new ResponseConverter();
    }

    public function testFromPsr()
    {
        $psrResponse = new Response();

        $opulenceResponse = $this->sut->fromPsr($psrResponse);

        $this->assertInstanceOf(\Opulence\Http\Responses\Response::class, $opulenceResponse);
        $this->assertSame($psrResponse->getStatusCode(), $opulenceResponse->getStatusCode());
    }
}
