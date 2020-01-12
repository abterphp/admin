<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Validation\Factory;

use AbterPhp\Admin\TestDouble\Validation\StubRulesFactory;
use AbterPhp\Framework\Validation\Rules\Forbidden;
use AbterPhp\Framework\Validation\Rules\Uuid;
use Opulence\Validation\IValidator;
use Opulence\Validation\Rules\Factories\RulesFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ApiClientTest extends TestCase
{
    /** @var ApiClient - System Under Test */
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

        $this->sut = new ApiClient($this->rulesFactoryMock);
    }

    /**
     * @return array
     */
    public function createValidatorProvider(): array
    {
        return [
            'empty-data'                          => [
                [],
                false,
            ],
            'valid-data'                          => [
                [
                    'user_id'     => '5c032f90-bf10-4a77-81aa-b0b1254a8f66',
                    'description' => 'foo',
                ],
                true,
            ],
            'valid-data-missing-all-not-required' => [
                [
                    'user_id'     => '5c032f90-bf10-4a77-81aa-b0b1254a8f66',
                    'description' => 'foo',
                ],
                true,
            ],
            'valid-data-with-admin-resources'     => [
                [
                    'user_id'            => '5c032f90-bf10-4a77-81aa-b0b1254a8f66',
                    'description'        => 'foo',
                    'admin_resource_ids' => ['bar', 'baz'],
                ],
                true,
            ],
            'invalid-has-id'                      => [
                [
                    'id'          => '465c91df-9cc7-47e2-a2ef-8fe645753148',
                    'user_id'     => '5c032f90-bf10-4a77-81aa-b0b1254a8f66',
                    'description' => 'foo',
                ],
                false,
            ],
            'invalid-user-id-missing'             => [
                [
                    'description' => 'foo',
                ],
                false,
            ],
            'invalid-user-id-not-uuid'            => [
                [
                    'user_id'     => '5c032f90-bf10-4a77-81aa-b0b1254a8f6',
                    'description' => 'foo',
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
