<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Validation\Factory;

use AbterPhp\Admin\TestDouble\Validation\StubRulesFactory;
use AbterPhp\Framework\Validation\Rules\Uuid;
use Opulence\Validation\IValidator;
use Opulence\Validation\Rules\Factories\RulesFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class NewUserTest extends TestCase
{
    /** @var User - System Under Test */
    protected $sut;

    /** @var RulesFactory|MockObject */
    protected $rulesFactoryMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->rulesFactoryMock = StubRulesFactory::createRulesFactory($this, ['uuid' => new Uuid()]);

        $this->sut = new NewUser($this->rulesFactoryMock);
    }

    /**
     * @return array
     */
    public function createValidatorProvider(): array
    {
        return [
            'empty-data' => [
                [],
                false,
            ],
            'valid-data' => [
                [
                    'id'                 => '465c91df-9cc7-47e2-a2ef-8fe645753148',
                    'username'           => 'foo',
                    'email'              => 'user@example.com',
                    'user_group_ids'     => [
                        '5c032f90-bf10-4a77-81aa-b0b1254a8f66',
                        '96aaef56-0e11-4f1c-b407-a8b65ff8e647',
                    ],
                    'user_language_id'   => 'df99af41-82fd-4865-a3d1-6a2eebf0951c',
                    'password'           => 'foo',
                    'password_confirmed' => 'foo',
                ],
                true,
            ],
            'valid-data-missing-all-not-required' => [
                [
                    'username'           => 'foo',
                    'email'              => 'user@example.com',
                    'user_language_id'   => 'df99af41-82fd-4865-a3d1-6a2eebf0951c',
                    'password'           => 'foo',
                    'password_confirmed' => 'foo',
                ],
                true,
            ],
            'invalid-id-not-uuid' => [
                [
                    'id'                 => '465c91df-9cc7-47e2-a2ef-8fe64575314',
                    'username'           => 'foo',
                    'email'              => 'user@example.com',
                    'user_group_ids'     => [
                        '5c032f90-bf10-4a77-81aa-b0b1254a8f66',
                        '96aaef56-0e11-4f1c-b407-a8b65ff8e647',
                    ],
                    'user_language_id'   => 'df99af41-82fd-4865-a3d1-6a2eebf0951c',
                    'password'           => 'foo',
                    'password_confirmed' => 'foo',
                ],
                false,
            ],
            'invalid-username-missing' => [
                [
                    'id'                 => '465c91df-9cc7-47e2-a2ef-8fe645753148',
                    'username'           => '',
                    'email'              => 'user@example.com',
                    'user_group_ids'     => [
                        '5c032f90-bf10-4a77-81aa-b0b1254a8f66',
                        '96aaef56-0e11-4f1c-b407-a8b65ff8e647',
                    ],
                    'user_language_id'   => 'df99af41-82fd-4865-a3d1-6a2eebf0951c',
                    'password'           => 'foo',
                    'password_confirmed' => 'foo',
                ],
                false,
            ],
            'invalid-email-not-valid' => [
                [
                    'id'                 => '465c91df-9cc7-47e2-a2ef-8fe645753148',
                    'username'           => 'foo',
                    'email'              => 'user@@example.com',
                    'user_group_ids'     => [
                        '5c032f90-bf10-4a77-81aa-b0b1254a8f66',
                        '96aaef56-0e11-4f1c-b407-a8b65ff8e647',
                    ],
                    'user_language_id'   => 'df99af41-82fd-4865-a3d1-6a2eebf0951c',
                    'password'           => 'foo',
                    'password_confirmed' => 'foo',
                ],
                false,
            ],
            'invalid-email-empty' => [
                [
                    'id'                 => '465c91df-9cc7-47e2-a2ef-8fe645753148',
                    'username'           => 'foo',
                    'email'              => '',
                    'user_group_ids'     => [
                        '5c032f90-bf10-4a77-81aa-b0b1254a8f66',
                        '96aaef56-0e11-4f1c-b407-a8b65ff8e647',
                    ],
                    'user_language_id'   => 'df99af41-82fd-4865-a3d1-6a2eebf0951c',
                    'password'           => 'foo',
                    'password_confirmed' => 'foo',
                ],
                false,
            ],
            'invalid-email-missing' => [
                [
                    'id'                 => '465c91df-9cc7-47e2-a2ef-8fe645753148',
                    'username'           => 'foo',
                    'user_group_ids'     => [
                        '5c032f90-bf10-4a77-81aa-b0b1254a8f66',
                        '96aaef56-0e11-4f1c-b407-a8b65ff8e647',
                    ],
                    'user_language_id'   => 'df99af41-82fd-4865-a3d1-6a2eebf0951c',
                    'password'           => 'foo',
                    'password_confirmed' => 'foo',
                ],
                false,
            ],
            'invalid-user-language-id-not-uuid' => [
                [
                    'id'                 => '465c91df-9cc7-47e2-a2ef-8fe645753148',
                    'username'           => 'foo',
                    'email'              => 'user@example.com',
                    'user_group_ids'     => [],
                    'user_language_id'   => 'df99af41-82fd-4865-a3d1-6a2eebf0951',
                    'password'           => 'foo',
                    'password_confirmed' => 'foo',
                ],
                false,
            ],
            'invalid-user-language-id-empty' => [
                [
                    'id'                 => '465c91df-9cc7-47e2-a2ef-8fe645753148',
                    'username'           => 'foo',
                    'email'              => 'user@example.com',
                    'user_group_ids'     => [
                        '5c032f90-bf10-4a77-81aa-b0b1254a8f66',
                        '96aaef56-0e11-4f1c-b407-a8b65ff8e647',
                    ],
                    'user_language_id'   => '',
                    'password'           => 'foo',
                    'password_confirmed' => 'foo',
                ],
                false,
            ],
            'invalid-user-language-id-missing' => [
                [
                    'id'                 => '465c91df-9cc7-47e2-a2ef-8fe645753148',
                    'username'           => 'foo',
                    'email'              => 'user@example.com',
                    'user_group_ids'     => [
                        '5c032f90-bf10-4a77-81aa-b0b1254a8f66',
                        '96aaef56-0e11-4f1c-b407-a8b65ff8e647',
                    ],
                    'password'           => 'foo',
                    'password_confirmed' => 'foo',
                ],
                false,
            ],
            'invalid-passwords-dont-match' => [
                [
                    'id'                 => '465c91df-9cc7-47e2-a2ef-8fe645753148',
                    'username'           => 'foo',
                    'email'              => 'user@example.com',
                    'user_group_ids'     => [
                        '5c032f90-bf10-4a77-81aa-b0b1254a8f66',
                        '96aaef56-0e11-4f1c-b407-a8b65ff8e647',
                    ],
                    'user_language_id'   => 'df99af41-82fd-4865-a3d1-6a2eebf0951c',
                    'password'           => 'foo',
                    'password_confirmed' => 'bar',
                ],
                false,
            ],
        ];
    }

    /**
     * @dataProvider createValidatorProvider
     *
     * @param array $data
     * @param bool  $expectedResult
     */
    public function testCreateValidator(array $data, bool $expectedResult)
    {
        $validator = $this->sut->createValidator();

        $this->assertInstanceOf(IValidator::class, $validator);

        $actualResult = $validator->isValid($data);

        $this->assertSame($expectedResult, $actualResult);
    }
}
