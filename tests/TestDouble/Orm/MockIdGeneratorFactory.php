<?php

declare(strict_types=1);

namespace AbterPhp\Admin\TestDouble\Orm;

use Opulence\Orm\Ids\Generators\IIdGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MockIdGeneratorFactory
{
    /**
     * @param TestCase $testCase
     * @param string   ...$ids
     *
     * @return IIdGenerator|MockObject
     */
    public static function create(TestCase $testCase, string ...$ids): IIdGenerator
    {
        /** @var IIdGenerator $idGeneratorMock */
        $idGeneratorMock = $testCase->getMockBuilder(IIdGenerator::class)
            ->getMock();

        foreach ($ids as $idx => $returnValue) {
            $idGeneratorMock->expects($testCase->at($idx))->method('generate')->willReturn($returnValue);
        }

        return $idGeneratorMock;
    }
}
