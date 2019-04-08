<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Databases\Queries;

use AbterPhp\Framework\Orm\DataMappers\SqlTestCase;

class AdminResourceAuthLoaderTest extends SqlTestCase
{
    /** @var AdminResourceAuthLoader */
    protected $sut;

    public function setUp()
    {
        parent::setUp();

        $this->sut = new AdminResourceAuthLoader($this->connectionPoolMock);
    }

    public function testLoadAll()
    {
        $userGroupIdentifier     = 'foo';
        $adminResourceIdentifier = 'bar';

        $sql          = 'SELECT ug.identifier AS v0, ar.identifier AS v1 FROM user_groups_admin_resources AS ugar INNER JOIN admin_resources AS ar ON ugar.admin_resource_id = ar.id AND ar.deleted = 0 INNER JOIN user_groups AS ug ON ugar.user_group_id = ug.id AND ug.deleted = 0'; // phpcs:ignore
        $valuesToBind = [];
        $returnValues = [
            [
                'v0' => $userGroupIdentifier,
                'v1' => $adminResourceIdentifier,
            ],
        ];

        $this->prepare($this->readConnectionMock, $sql, $this->createReadStatement($valuesToBind, $returnValues));

        $actualResult = $this->sut->loadAll();

        $this->assertEquals($returnValues, $actualResult);
    }

    /**
     * @param array  $expectedData
     * @param object $entity
     */
    protected function assertEntity(array $expectedData, $entity)
    {
    }
}
