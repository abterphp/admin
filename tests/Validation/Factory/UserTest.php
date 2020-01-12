<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Validation\Factory;

use AbterPhp\Admin\TestDouble\Validation\StubRulesFactory;
use AbterPhp\Framework\Http\Service\Execute\IRepoService;
use AbterPhp\Framework\Validation\Rules\Forbidden;
use AbterPhp\Framework\Validation\Rules\Uuid;
use Opulence\Validation\IValidator;
use Opulence\Validation\Rules\Factories\RulesFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /** @var User - System Under Test */
    protected $sut;

    /** @var RulesFactory|MockObject */
    protected $rulesFactoryMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->rulesFactoryMock = StubRulesFactory::createRulesFactory(
            $this,
            ['uuid' => new Uuid(), 'forbidden' => new Forbidden()]
        );

        $this->sut = new User($this->rulesFactoryMock);
    }

    /**
     * @return array
     */
    public function createValidatorExistingProvider(): array
    {
        return [
            'empty-data'                          => [
                [],
                false,
            ],
            'valid-data'                          => [
                [
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
                    'username'         => 'foo',
                    'email'            => 'user@example.com',
                    'user_language_id' => 'df99af41-82fd-4865-a3d1-6a2eebf0951c',
                ],
                true,
            ],
            'invalid-has-id'                      => [
                [
                    'id'               => '465c91df-9cc7-47e2-a2ef-8fe645753148',
                    'username'         => 'foo',
                    'email'            => 'user@example.com',
                    'user_language_id' => 'df99af41-82fd-4865-a3d1-6a2eebf0951c',
                ],
                false,
            ],
            'invalid-username-missing'            => [
                [
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
            'invalid-email-not-valid'             => [
                [
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
            'invalid-email-empty'                 => [
                [
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
            'invalid-email-missing'               => [
                [
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
            'invalid-user-language-id-not-uuid'   => [
                [
                    'username'           => 'foo',
                    'email'              => 'user@example.com',
                    'user_group_ids'     => [],
                    'user_language_id'   => 'df99af41-82fd-4865-a3d1-6a2eebf0951',
                    'password'           => 'foo',
                    'password_confirmed' => 'foo',
                ],
                false,
            ],
            'invalid-user-language-id-empty'      => [
                [
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
            'invalid-user-language-id-missing'    => [
                [
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
            'invalid-passwords-dont-match'        => [
                [
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
     * @dataProvider createValidatorExistingProvider
     *
     * @param array $data
     * @param bool  $expectedResult
     */
    public function testCreateValidatorExisting(array $data, bool $expectedResult)
    {
        $this->runTestCreateValidator(IRepoService::UPDATE, $data, $expectedResult);
    }

    /**
     * @return array
     */
    public function createValidatorNewProvider(): array
    {
        $data = $this->createValidatorExistingProvider();

        $data['valid-data-missing-all-not-required'] = [
            [
                'username'           => 'foo',
                'email'              => 'user@example.com',
                'user_language_id'   => 'df99af41-82fd-4865-a3d1-6a2eebf0951c',
                'password'           => 'foo',
                'password_confirmed' => 'foo',
            ],
            true,
        ];

        return $data;
    }

    /**
     * @dataProvider createValidatorNewProvider
     *
     * @param array $data
     * @param bool  $expectedResult
     */
    public function testCreateValidatorNew(array $data, bool $expectedResult)
    {
        $this->runTestCreateValidator(IRepoService::CREATE, $data, $expectedResult);
    }

    /**
     * @dataProvider createValidatorExistingProvider
     *
     * @param array $additionalData
     * @param array $data
     * @param bool  $expectedResult
     */
    public function runTestCreateValidator(int $additionalData, array $data, bool $expectedResult)
    {
        $this->sut->setAdditionalData($additionalData);

        $validator = $this->sut->createValidator();

        $this->assertInstanceOf(IValidator::class, $validator);

        $actualResult = $validator->isValid($data);

        $this->assertSame($expectedResult, $actualResult);
    }
}
