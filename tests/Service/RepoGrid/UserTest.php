<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Service\RepoGrid;

use AbterPhp\Admin\Grid\Factory\User as GridFactory;
use AbterPhp\Admin\Orm\UserRepo as Repo;
use AbterPhp\Framework\Databases\Queries\FoundRows;
use AbterPhp\Framework\Grid\IGrid;
use Casbin\Enforcer;
use Opulence\Http\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /** @var User - System Under Test */
    protected $sut;

    /** @var Enforcer|MockObject */
    protected $enforcerMock;

    /** @var Repo|MockObject */
    protected $repoMock;

    /** @var FoundRows|MockObject */
    protected $foundRowsMock;

    /** @var GridFactory|MockObject */
    protected $gridFactoryMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->enforcerMock = $this->getMockBuilder(Enforcer::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $this->repoMock = $this->getMockBuilder(Repo::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getPage'])
            ->getMock();

        $this->foundRowsMock = $this->getMockBuilder(FoundRows::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get'])
            ->getMock();

        $this->gridFactoryMock = $this->getMockBuilder(GridFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['createGrid'])
            ->getMock();

        $this->sut = new User(
            $this->enforcerMock,
            $this->repoMock,
            $this->foundRowsMock,
            $this->gridFactoryMock
        );
    }

    public function testCreateAndPopulate()
    {
        $baseUrl = '/foo';

        /** @var Collection|MockObject $query */
        $queryStub = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAll', 'get'])
            ->getMock();

        /** @var IGrid|MockObject $query */
        $gridStub = $this->getMockBuilder(IGrid::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'getPageSize',
                'getSortConditions',
                'getWhereConditions',
                'getSqlParams',
                'setTotalCount',
                'setEntities',
                // component
                'find',
                'findFirstChild',
                'collect',
                'insertBefore',
                'insertAfter',
                'replace',
                'remove',
                // tag
                'setTag',
                'getAttributes',
                'hasAttribute',
                'getAttribute',
                'unsetAttribute',
                'unsetAttributeValue',
                'setAttributes',
                'addAttributes',
                'setAttribute',
                'appendToAttributes',
                'appendToAttribute',
                'appendToClass',
                // node container
                'getNodes',
                'getDescendantNodes',
                'getExtendedNodes',
                'getExtendedDescendantNodes',
                // node
                'setContent',
                'hasIntent',
                'getIntents',
                'setIntent',
                'addIntent',
                'setTranslator',
                'getTranslator',
                'isMatch',
                '__toString',
                // array
                'offsetExists',
                'offsetGet',
                'offsetSet',
                'offsetUnset',
                // countable
                'count',
                // iterator
                'current',
                'next',
                'key',
                'valid',
                'rewind',
            ])
            ->getMock();

        $this->gridFactoryMock
            ->expects($this->any())
            ->method('createGrid')
            ->willReturn($gridStub);

        $actualResult = $this->sut->createAndPopulate($queryStub, $baseUrl);

        $this->assertSame($gridStub, $actualResult);
    }
}
