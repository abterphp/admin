<?php

declare(strict_types=1);

namespace AbterPhp\Admin\TestCase\Orm;

use AbterPhp\Framework\TestCase\Database\QueryTestCase;

abstract class DataMapperTestCase extends QueryTestCase
{
    /**
     * @param array $expectedData
     * @param array $collection
     */
    protected function assertCollection(array $expectedData, $collection)
    {
        $this->assertNotNull($collection);
        $this->assertIsArray($collection);
        $this->assertCount(count($expectedData), $collection);

        foreach ($collection as $key => $entity) {
            $this->assertEntity($expectedData[$key], $entity);
        }
    }

    /**
     * @param array  $expectedData
     * @param object $entity
     */
    abstract protected function assertEntity(array $expectedData, $entity);
}
