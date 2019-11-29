<?php declare(strict_types = 1);

namespace Moi\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MissingMethodFromReflectionException;
use PHPStan\Reflection\ParameterReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Reflection\Php\PhpParameterReflection;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\ArrayType;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\VerbosityLevel;

class MethodParameter implements Rule
{
    /**
     * @param MethodReflection $methodReflection
     * @param ParameterReflection $parameterReflection
     * @return string|null
     */
    private function checkMethodParameter(MethodReflection $methodReflection, ParameterReflection $parameterReflection): ?string
    {
        if (!$methodReflection->isPublic()) {
            return null;
        }

        $parameterType = $parameterReflection->getType();

        if ($parameterReflection instanceof PhpParameterReflection
            && $parameterReflection->getNativeType() instanceof MixedType
            && ($parameterReflection->getPhpDocType() instanceof ArrayType
                || $parameterReflection->getPhpDocType() instanceof ObjectType)) {
            return sprintf(
                'Method %s::%s() has parameter $%s can be type-hinted to "%s $%s"',
                $methodReflection->getDeclaringClass()->getDisplayName(),
                $methodReflection->getName(),
                $parameterReflection->getName(),
                $parameterType->describe(VerbosityLevel::typeOnly()),
                $parameterReflection->getName()
            );
        }

        return null;
    }

    /**
     * @return string Class implementing \PhpParser\Node
     */
    public function getNodeType(): string
    {
        return Node\Stmt\ClassMethod::class;
    }

    /**
     * @param Node $node
     * @param Scope $scope
     * @return array (string|RuleError)[] errors
     * @throws MissingMethodFromReflectionException
     * @throws ShouldNotHappenException
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (!$scope->isInClass()) {
            throw new ShouldNotHappenException();
        }
        $methodReflection = $scope->getClassReflection()->getNativeMethod($node->name->name);
        $messages = [];
        foreach (ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getParameters() as $parameterReflection) {
            $message = $this->checkMethodParameter($methodReflection, $parameterReflection);
            if ($message === null) {
                continue;
            }
            $messages[] = $message;
        }
        return $messages;
    }
}
