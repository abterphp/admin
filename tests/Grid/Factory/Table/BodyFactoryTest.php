<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Grid\Factory\Table;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Grid\Component\Body;
use PHPUnit\Framework\TestCase;

class BodyFactoryTest extends TestCase
{
    public function testCreate()
    {
        $getters      = ['foo' => '__toString'];
        $rowArguments = [Html5::ATTR_CLASS => 'foo'];

        $sut = new BodyFactory();

        $body = $sut->create($getters, $rowArguments, null);

        $this->assertInstanceOf(Body::class, $body);
    }
}
