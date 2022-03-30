<?php

namespace SprykerSdk\Integrator\ManifestGenerator;

use SprykerSdk\Integrator\ManifestGenerator\Exception\ValueExtractorException;
use PhpParser\Node\Stmt\Return_;

class ModuleConfigManifestStrategy extends AbstractManifestStrategy implements ManifestStrategyInterface
{
    /**
     * @var string
     */
    public const MANIFEST_KEY = 'configure-module';

    /**
     * @var array<string>
     */
    protected const APPLICATION_LAYERS = [
        'Yves',
        'Zed',
        'Client',
        'Service',
        'Shared',
    ];

    /**
     * @param string $fileName
     *
     * @return bool
     */
    public function isApplicable(string $fileName): bool
    {
        $regex = sprintf('/.+\/src\/[a-zA-Z]+\/(%s)\/[a-zA-Z]+\/[a-zA-Z]+Config\.php/', implode('|', static::APPLICATION_LAYERS));

        return preg_match($regex, $fileName);
    }

    /**
     * @param string $fileName
     * @param string $originalFileName
     * @param array $existingChanges
     *
     * @return array
     */
    public function generateManifestData(string $fileName, string $originalFileName, array $existingChanges = []): array
    {
        $syntaxTree = $this->buildSyntaxTree($fileName);
        $originalSyntaxTree = [];

        if (file_exists($originalFileName)) {
            $originalSyntaxTree = $this->buildSyntaxTree($originalFileName);
        }

        $data = $this->compareClasses($syntaxTree, $originalSyntaxTree);

        return $this->buildManifestArray($data, $existingChanges);
    }

    /**
     * @param array<\PhpParser\Node> $currentSyntaxTree
     * @param array<\PhpParser\Node> $originalSyntaxTree
     *
     * @return array<string, array>
     */
    protected function compareClasses(array $currentSyntaxTree, array $originalSyntaxTree): array
    {
        $originalClassMethods = [];
        $originalClassConstants = [];
        $namespace = $this->getNameSpace($currentSyntaxTree);
        if ($originalSyntaxTree) {
            $originalClass = $this->getClassStatement($originalSyntaxTree);
            $originalClassMethods = $this->indexMethodsByName($originalClass->getMethods());
            $originalClassConstants = $this->indexConstantByName($originalClass->getConstants());
        }
        $currentClass = $this->getClassStatement($currentSyntaxTree);
        $currentClassMethods = $this->indexMethodsByName($currentClass->getMethods());
        $currentClassConstants = $this->indexConstantByName($currentClass->getConstants());

        return array_merge(
            $this->gatherConfigMethods($currentClassMethods, $originalClassMethods, $namespace),
            $this->gatherConstants($currentClassConstants, $originalClassConstants, $namespace),
        );
    }

    /**
     * @param array<\PhpParser\Node\Stmt\ClassMethod> $classMethods
     *
     * @return array<string, \PhpParser\Node\Stmt\ClassMethod>
     */
    protected function indexMethodsByName(array $classMethods): array
    {
        $indexedClassMethods = [];

        foreach ($classMethods as $classMethod) {
            $indexedClassMethods[$classMethod->name->toString()] = $classMethod;
        }

        return $indexedClassMethods;
    }

    /**
     * @param array<\PhpParser\Node\Stmt\ClassConst> $classConstants
     *
     * @return array<string, \PhpParser\Node\Const_>
     */
    protected function indexConstantByName(array $classConstants): array
    {
        $indexedClassConstants = [];
        foreach ($classConstants as $classConstant) {
            foreach ($classConstant->consts as $constant) {
                $indexedClassConstants[$constant->name->toString()] = $constant;
            }
        }

        return $indexedClassConstants;
    }

    /**
     * @param array<string, \PhpParser\Node\Stmt\ClassMethod> $currentMethods
     * @param array<string, \PhpParser\Node\Stmt\ClassMethod> $existingMethods
     * @param string $namespace
     *
     * @return array
     */
    protected function gatherConfigMethods(array $currentMethods, array $existingMethods, string $namespace): array
    {
        $methods = [];
        foreach ($currentMethods as $methodName => $method) {
            if (isset($existingMethods[$methodName]) || !($method->stmts[0] instanceof Return_)) {
                continue;
            }

            try {
                $extractedValueObject = $this->extractValueFromExpression($method->stmts[0]->expr);
            } catch (ValueExtractorException $exception) {
                // Not supported (yet)
                continue;
            }

            $value = $extractedValueObject->getValue();
            if ($extractedValueObject->isLiteral()) {
                $value = [
                    'value' => $extractedValueObject->getValue(),
                    'is_literal' => true,
                ];
            }

            $methods[] = [
                static::TARGET => sprintf('%s::%s', $namespace, $methodName),
                static::VALUE => $value,
            ];
        }

        return $methods;
    }

    /**
     * @param array<string, \PhpParser\Node\Const_> $currentConstants
     * @param array<string, \PhpParser\Node\Const_> $existingConstants
     * @param string $namespace
     *
     * @return array
     */
    protected function gatherConstants(array $currentConstants, array $existingConstants, string $namespace): array
    {
        $constants = [];

        foreach ($currentConstants as $constantName => $constant) {
            if (isset($existingConstants[$constantName])) {
                continue;
            }

            try {
                $extractedValueObject = $this->extractValueFromExpression($constant->value);
            } catch (ValueExtractorException $exception) {
                Log::write(
                    'info',
                    sprintf(
                        'Error in value extraction in a config constant %s. %s value expression is not supported.',
                        $constantName,
                        get_class($constant->value),
                    ),
                );

                continue;
            }

            $value = $extractedValueObject->getValue();
            if ($extractedValueObject->isLiteral()) {
                $value = [
                    'value' => $extractedValueObject->getValue(),
                    'is_literal' => true,
                ];
            }

            $constants[] = [
                static::TARGET => sprintf('%s::%s', $namespace, $constantName),
                static::VALUE => $value,
            ];
        }

        return $constants;
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, array> $manifests
     *
     * @return array<string, array>
     */
    protected function buildManifestArray(array $data, array $manifests): array
    {
        foreach ($data as $changes) {
            [$organization, $module] = $this->getOrganizationAndModuleName($changes[static::TARGET]);
            $manifests[$organization . '.' . $module][static::MANIFEST_KEY][] = $changes;
        }

        return $manifests;
    }
}
