<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Form\Factory;

use AbterPhp\Admin\Domain\Entities\User as Entity;
use AbterPhp\Admin\Domain\Entities\UserGroup;
use AbterPhp\Admin\Domain\Entities\UserLanguage;
use AbterPhp\Admin\Orm\UserGroupRepo;
use AbterPhp\Admin\Orm\UserLanguageRepo;
use AbterPhp\Framework\I18n\ITranslator;
use Opulence\Http\Requests\RequestMethods;
use Opulence\Sessions\ISession;
use Opulence\Sessions\Session;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /** @var ISession|MockObject */
    protected $sessionMock;

    /** @var ITranslator|MockObject */
    protected $translatorMock;

    /** @var UserGroupRepo|MockObject */
    protected $userGroupRepoMock;

    /** @var UserLanguageRepo|MockObject */
    protected $userLanguageRepoMock;

    /** @var User */
    protected $sut;

    public function setUp(): void
    {
        $this->sessionMock = $this->getMockBuilder(Session::class)
            ->setMethods(['get'])
            ->getMock();
        $this->sessionMock->expects($this->any())->method('get')->willReturnArgument(0);

        $this->translatorMock = $this->getMockBuilder(ITranslator::class)
            ->setMethods(['translate', 'canTranslate'])
            ->getMock();
        $this->translatorMock->expects($this->any())->method('translate')->willReturnArgument(0);

        $this->userGroupRepoMock = $this->getMockBuilder(UserGroupRepo::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAll'])
            ->getMock();

        $this->userLanguageRepoMock = $this->getMockBuilder(UserLanguageRepo::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAll'])
            ->getMock();

        $this->sut = new User(
            $this->sessionMock,
            $this->translatorMock,
            $this->userGroupRepoMock,
            $this->userLanguageRepoMock
        );
    }

    public function testCreate()
    {
        $action            = 'foo';
        $method            = RequestMethods::POST;
        $showUrl           = 'bar';
        $entityId          = '96368723-292f-4943-9903-83ad552fc118';
        $username          = 'zorro79';
        $email             = 'zorro79@example.com';
        $canLogin          = true;
        $isGravatarAllowed = true;
        $allUserGroups     = [
            new UserGroup('dc2abea5-8021-4228-882a-31b91fe3687a', 'ug-22', 'UG 22'),
            new UserGroup('75ccb863-ce02-4b2d-b655-af4c92f2dbe6', 'ug-73', 'UG 73'),
            new UserGroup('aff15988-2170-4b10-9aad-4ed2ea19f73e', 'ug-112', 'UG 112'),
            new UserGroup('143522ce-5e0e-4abb-8c4b-67d88ba90d9d', 'ug-432', 'UG 432'),
        ];
        $userGroups        = [
            $allUserGroups[0],
            $allUserGroups[2],
        ];
        $allUserLanguages  = [
            new UserLanguage('5027689a-39da-4810-b0b6-2ae42e387698', 'ul-52', 'UL 52'),
            new UserLanguage('afb423cc-6272-4ff4-8a62-e28cac6cb1d1', 'ul-77', 'UL 77'),
            new UserLanguage('38030970-10be-4b6b-9dc6-38d6a74310ca', 'ul-93', 'UL 93'),
            new UserLanguage('15c3f8ef-d5ff-4210-a5a2-3732f4073a1b', 'ul-94', 'UL 94'),
        ];
        $userLanguage      = $allUserLanguages[1];

        $this->userGroupRepoMock->expects($this->any())->method('getAll')->willReturn($allUserGroups);
        $this->userLanguageRepoMock->expects($this->any())->method('getAll')->willReturn($allUserLanguages);

        $entityMock = $this->createMockEntity();

        $entityMock->expects($this->any())->method('getId')->willReturn($entityId);
        $entityMock->expects($this->any())->method('getUsername')->willReturn($username);
        $entityMock->expects($this->any())->method('getEmail')->willReturn($email);
        $entityMock->expects($this->any())->method('canLogin')->willReturn($canLogin);
        $entityMock->expects($this->any())->method('isGravatarAllowed')->willReturn($isGravatarAllowed);
        $entityMock->expects($this->any())->method('getUserGroups')->willReturn($userGroups);
        $entityMock->expects($this->any())->method('getUserLanguage')->willReturn($userLanguage);

        $form = (string)$this->sut->create($action, $method, $showUrl, $entityMock);

        $this->assertStringContainsString($action, $form);
        $this->assertStringContainsString($showUrl, $form);
        $this->assertStringContainsString('CSRF', $form);
        $this->assertStringContainsString('POST', $form);
        $this->assertStringContainsString('username', $form);
        $this->assertStringContainsString('email', $form);
        $this->assertStringContainsString('password', $form);
        $this->assertStringContainsString('password_confirmed', $form);
        $this->assertStringContainsString('raw_password', $form);
        $this->assertStringContainsString('raw_password_confirmed', $form);
        $this->assertStringContainsString('can_login', $form);
        $this->assertStringContainsString('is_gravatar_allowed', $form);
        $this->assertStringContainsString('user_group_ids', $form);
        $this->assertStringContainsString('user_language_id', $form);
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
            ->setMethods(
                [
                    'getId',
                    'getUsername',
                    'getEmail',
                    'canLogin',
                    'isGravatarAllowed',
                    'getUserGroups',
                    'getUserLanguage',
                ]
            )
            ->getMock();

        return $entityMock;
    }
}
