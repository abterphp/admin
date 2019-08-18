<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Form\Factory;

use AbterPhp\Admin\Domain\Entities\AdminResource;
use AbterPhp\Admin\Domain\Entities\ApiClient as Entity;
use AbterPhp\Admin\Orm\AdminResourceRepo;
use AbterPhp\Framework\Html\Component\ButtonFactory;
use AbterPhp\Framework\I18n\ITranslator;
use Opulence\Http\Requests\RequestMethods;
use Opulence\Sessions\ISession;
use Opulence\Sessions\Session;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ApiClientTest extends TestCase
{
    /** @var ApiClient - System Under Test */
    protected $sut;

    /** @var ISession|MockObject */
    protected $sessionMock;

    /** @var ITranslator|MockObject */
    protected $translatorMock;

    /** @var AdminResourceRepo|MockObject */
    protected $adminResourceRepoMock;

    /** @var ButtonFactory|MockObject */
    protected $buttonFactoryMock;

    public function setUp(): void
    {
        $this->sessionMock = $this->getMockBuilder(Session::class)
            ->onlyMethods(['get'])
            ->getMock();
        $this->sessionMock->expects($this->any())->method('get')->willReturnArgument(0);

        $this->translatorMock = $this->getMockBuilder(ITranslator::class)
            ->onlyMethods(['translate', 'canTranslate'])
            ->getMock();
        $this->translatorMock->expects($this->any())->method('translate')->willReturnArgument(0);

        $this->adminResourceRepoMock = $this->getMockBuilder(AdminResourceRepo::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getByUserId'])
            ->getMock();

        $this->buttonFactoryMock = $this->getMockBuilder(ButtonFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['createFromUrl', 'createFromName', 'createSimple', 'createWithIcon'])
            ->getMock();

        $this->sut = new ApiClient(
            $this->sessionMock,
            $this->translatorMock,
            $this->adminResourceRepoMock,
            $this->buttonFactoryMock
        );
    }

    public function testCreate()
    {
        $action            = 'foo';
        $method            = RequestMethods::POST;
        $showUrl           = 'bar';
        $entityId          = '26f69be3-fa57-4ad1-8c58-5f4631040ece';
        $userId            = '3c1c312b-c6c1-40a5-a957-5830ecde8a5a';
        $description       = 'foo';
        $secret            = 'bar';
        $allAdminResources = [
            new AdminResource('8a42e773-975d-41bd-9061-57ee6c381e68', 'ar-21'),
            new AdminResource('5180d59e-3b79-4c3b-8877-7df8086a8879', 'ar-47'),
            new AdminResource('08e82847-9342-42ae-8563-ef2bae335c7a', 'ar-64'),
            new AdminResource('c2d3f41c-15ba-4664-8393-8024bf650d21', 'ar-187'),
        ];
        $adminResources    = [$allAdminResources[1], $allAdminResources[3]];

        $this->adminResourceRepoMock
            ->expects($this->any())
            ->method('getByUserId')
            ->willReturn($allAdminResources);

        $entityStub = new Entity($entityId, $userId, $description, $secret, $adminResources);

        $form = (string)$this->sut->create($action, $method, $showUrl, $entityStub);

        $this->assertStringContainsString($action, $form);
        $this->assertStringContainsString($showUrl, $form);
        $this->assertStringContainsString('CSRF', $form);
        $this->assertStringContainsString('POST', $form);
        $this->assertStringContainsString('description', $form);
        $this->assertStringContainsString('secret', $form);
        $this->assertStringContainsString('admin_resource_ids', $form);
        $this->assertStringContainsString('selected', $form);
        $this->assertStringContainsString('button', $form);
    }

    /**
     * @return MockObject|Entity
     */
    protected function createMockEntity()
    {
        $entityMock = $this->getMockBuilder(Entity::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'getId',
                    'getIdentifier',
                    'getName',
                    'getAdminResources',
                ]
            )
            ->getMock();

        return $entityMock;
    }
}
