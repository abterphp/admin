<?php

namespace AbterPhp\Admin\TestDouble\Validation;

use AbterPhp\Framework\Validation\Rules\Uuid;
use Opulence\Validation\Rules\Errors\Compilers\ICompiler;
use Opulence\Validation\Rules\Errors\ErrorTemplateRegistry;
use Opulence\Validation\Rules\Factories\RulesFactory;
use Opulence\Validation\Rules\IRule;
use Opulence\Validation\Rules\RuleExtensionRegistry;
use Opulence\Validation\Rules\Rules;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class StubRulesFactory
{
    /**
     * @return IRule[]
     */
    protected static function createDefaultRules()
    {
        return [
            'uuid' => new Uuid(),
        ];
    }

    /**
     * @param TestCase     $testCase
     * @param IRule[]|null $rules
     *
     * @return RulesFactory|MockObject
     */
    public static function createRulesFactory(TestCase $testCase, array $rules = null): RulesFactory
    {
        $rulesFactoryMock = $testCase->getMockBuilder(RulesFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['createRules'])
            ->getMock();

        $rulesMock = static::createRules($testCase, $rules);

        $rulesFactoryMock
            ->expects($testCase->any())
            ->method('createRules')
            ->willReturnCallback(function () use ($rulesMock) {
                return clone $rulesMock;
            });

        return $rulesFactoryMock;
    }

    /**
     * @param TestCase     $testCase
     * @param IRule[]|null $rules
     *
     * @return Rules
     */
    public static function createRules(TestCase $testCase, ?array $rules = null): Rules
    {
        $rules = $rules ?: static::createDefaultRules();

        /** @var RuleExtensionRegistry|MockObject $ruleExtensionRegistryStub */
        $ruleExtensionRegistryStub = $testCase->getMockBuilder(RuleExtensionRegistry::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['hasRule', 'getRule'])
            ->getMock();

        /** @var ErrorTemplateRegistry|MockObject $errorTemplateRegistryStub */
        $errorTemplateRegistryStub = $testCase->getMockBuilder(ErrorTemplateRegistry::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        /** @var ICompiler|MockObject $compilerStub */
        $compilerStub = $testCase->getMockBuilder(ICompiler::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $rulesStub = new Rules($ruleExtensionRegistryStub, $errorTemplateRegistryStub, $compilerStub);

        $ruleExtensionRegistryStub
            ->expects($testCase->any())
            ->method('hasRule')
            ->willReturnCallback(function (string $methodName) use ($rules): bool {
                return array_key_exists($methodName, $rules);
            });

        $ruleExtensionRegistryStub
            ->expects($testCase->any())
            ->method('getRule')
            ->willReturnCallback(function (string $methodName) use ($rules): IRule {
                return $rules[$methodName];
            });

        return $rulesStub;
    }
}
