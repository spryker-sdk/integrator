<?php

namespace SprykerSdk\Integrator\ManifestGenerator;

use SprykerSdk\Integrator\ManifestGenerator\ArrayElementExtractor\ArrayElementNamespaceExtractor\ConstArrayElementNamespaceExtractor;
use SprykerSdk\Integrator\ManifestGenerator\ArrayElementExtractor\ConstExtractor;
use PhpParser\Node\Stmt;

class ArrayConfigElementManifestStrategy extends AbstractManifestStrategy implements ManifestStrategyInterface
{
    /**
     * @var string
     */
    public const MANIFEST_KEY = 'add-config-array-element';

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
        $regex = sprintf(
            '/.+\/src\/[a-zA-Z]+\/(%s)\/[a-zA-Z]+\/[a-zA-Z]+Config\.php/',
            implode('|', static::APPLICATION_LAYERS),
        );

        return preg_match($regex, $fileName);
    }

    /**
     * @inheritDoc
     */
    public function generateManifestData(string $fileName, string $originalFileName, array $existingChanges = []): array
    {
        $syntaxTree = $this->buildSyntaxTree($fileName);
        $originalSyntaxTree = $this->buildSyntaxTree($originalFileName);

        $data = $this->compareClasses($syntaxTree, $originalSyntaxTree);

        return $this->buildManifestArray($data, $this->getNameSpace($syntaxTree), $existingChanges);
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
        if ($originalSyntaxTree) {
            $originalClass = $this->getClassStatement($originalSyntaxTree);
            $originalClassMethods = $this->indexMethodsByName($originalClass->getMethods());
        }

        $currentClassMethods = [];
        if ($currentSyntaxTree) {
            $currentClass = $this->getClassStatement($currentSyntaxTree);
            $currentClassMethods = $this->indexMethodsByName($currentClass->getMethods());
        }

        return $this->gatherArrayElementsIndexedByMethod($currentClassMethods, $originalClassMethods);
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
     * @param array<\PhpParser\Node\Stmt\ClassMethod> $classMethods
     * @param array<\PhpParser\Node\Stmt\ClassMethod> $classMethodsToCompare
     *
     * @return array<string, array<string>>
     */
    protected function gatherArrayElementsIndexedByMethod(array $classMethods, array $classMethodsToCompare): array
    {
        $result = [];

        foreach ($classMethods as $methodName => $classMethod) {
            $extractorStack = $this->createArrayElementExtractorStack();

            $arrayElementsFromClass = $this->extractArrayElementsList($classMethod->stmts[0], $extractorStack);
            $arrayElementsFromClassToCompare = [];
            if (isset($classMethodsToCompare[$methodName])) {
                $arrayElementsFromClassToCompare = $this->extractArrayElementsList(
                    $classMethodsToCompare[$methodName]->stmts[0],
                    $extractorStack,
                );
            }

            $arrayElementsDiff = array_diff($arrayElementsFromClass, $arrayElementsFromClassToCompare);

            if ($arrayElementsDiff) {
                $result[$methodName] = $arrayElementsDiff;
            }
        }

        return $result;
    }

    /**
     * @return array<\SprykerSdk\Integrator\ManifestGenerator\ArrayElementExtractor\ArrayElementExtractorInterface>
     */
    protected function createArrayElementExtractorStack(): array
    {
        return [
            new ConstExtractor(),
        ];
    }

    /**
     * @param \PhpParser\Node\Stmt $statement
     * @param array<\SprykerSdk\Integrator\ManifestGenerator\ArrayElementExtractor\ArrayElementExtractorInterface> $extractors
     *
     * @return array
     */
    protected function extractArrayElementsList(Stmt $statement, array $extractors): array
    {
        $arrayElements = [];
        foreach ($extractors as $extractor) {
            if ($extractor->isApplicable($statement)) {
                $arrayElements = array_merge($extractor->extract($statement->expr), $arrayElements);
            }
        }

        return $arrayElements;
    }

    /**
     * @param array<string, array> $arrayElementChanges
     * @param string $namespace
     * @param array<string, array> $manifests
     *
     * @return array<string, array>
     */
    protected function buildManifestArray(array $arrayElementChanges, string $namespace, array $manifests): array
    {
        $namespaceExtractors = $this->getNamespaceExtractorCollection();
        foreach ($arrayElementChanges as $methodName => $arrayElements) {
            foreach ($arrayElements as $arrayElement) {
                $targetNamespace = $this->findArrayElementNamespace($arrayElement, $namespaceExtractors);
                if (!$targetNamespace) {
                    $targetNamespace = $namespace;
                }
                [$organization, $module] = $this->getOrganizationAndModuleName($targetNamespace);
                $manifests[$organization . '.' . $module][static::MANIFEST_KEY][] = [
                    static::TARGET => sprintf('%s::%s', $namespace, $methodName),
                    static::VALUE => $arrayElement,
                ];
            }
        }

        return $manifests;
    }

    /**
     * @return array<\SprykerSdk\Integrator\ManifestGenerator\ArrayElementExtractor\ArrayElementNamespaceExtractor\ArrayElementNamespaceExtractorInterface>
     */
    protected function getNamespaceExtractorCollection(): array
    {
        return [
            new ConstArrayElementNamespaceExtractor(),
        ];
    }

    /**
     * @param mixed $arrayElement
     * @param array<\SprykerSdk\Integrator\ManifestGenerator\ArrayElementExtractor\ArrayElementNamespaceExtractor\ArrayElementNamespaceExtractorInterface> $namespaceArrayExtractorCollection
     *
     * @return string|null
     */
    protected function findArrayElementNamespace($arrayElement, array $namespaceArrayExtractorCollection): ?string
    {
        foreach ($namespaceArrayExtractorCollection as $namespaceArrayExtractor) {
            if ($namespaceArrayExtractor->isApplicable($arrayElement)) {
                return $namespaceArrayExtractor->extractNamespace($arrayElement);
            }
        }

        return null;
    }
}
