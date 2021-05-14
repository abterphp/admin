<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Grid\Factory\Table;

use AbterPhp\Framework\Grid\Component\Body;
use PHPUnit\Framework\TestCase;

class BodyFactoryTest extends TestCase
{
    public function testCreate()
    {
        $sut = new BodyFactory();

        $getters = ['foo' => '__toString'];
        $body = $sut->create($getters);

        $this->assertInstanceOf(Body::class, $body);
    }
}
