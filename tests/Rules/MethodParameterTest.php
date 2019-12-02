<?php declare(strict_types = 1);

namespace PHPStan\Rules\Methods;

use Moi\PHPStan\Rule\MethodParameter;

class WrongCaseOfInheritedMethodRuleTest extends \PHPStan\Testing\RuleTestCase
{

    protected function getRule(): \PHPStan\Rules\Rule
    {
        return new MethodParameter();
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/data/WrongCase.php'], [
            [
                'Method WrongCase\WrongCase::foo() has parameter $array can be type-hinted to "array $array"',
                10,
            ],
            [
                'Method WrongCase\WrongCase::bar() has parameter $pdo can be type-hinted to "PDO $pdo"',
                18,
            ],
        ]);
    }
}
