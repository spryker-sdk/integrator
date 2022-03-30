<?php

namespace SprykerSdk\Integrator\ManifestGenerator;

use SprykerSdk\Integrator\ManifestGenerator\PluginExtractor\ReturnArrayPluginExtractor;
use SprykerSdk\Integrator\ManifestGenerator\PluginExtractor\ReturnClassPluginExtractor;
use SprykerSdk\Integrator\ManifestGenerator\PluginExtractor\VariableArrayPluginExtractor;
use PhpParser\Node\Stmt;

class PluginsManifestStrategy extends AbstractManifestStrategy implements ManifestStrategyInterface
{
    /**
     * @var string
     */
    public const MANIFEST_KEY_WIRE = 'wire-plugin';

    /**
     * @var string
     */
    public const MANIFEST_KEY_UNWIRE = 'unwire-plugin';

    /**
     * @param string $fileName
     *
     * @return bool
     */
    public function isApplicable(string $fileName): bool
    {
        return strpos($fileName, 'DependencyProvider.php') !== false;
    }

    /**
     * @inheritDoc
     */
    public function generateManifestData(string $fileName, string $originalFileName, array $existingChanges = []): array
    {
        $syntaxTree = $this->buildSyntaxTree($fileName);
        if (!$syntaxTree) {
            return [];
        }

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
        $currentClass = $this->getClassStatement($currentSyntaxTree);
        $currentClassMethods = $this->indexMethodsByName($currentClass->getMethods());

        return [
            static::WIRE => $this->gatherPluginsIndexedByMethod($currentClassMethods, $originalClassMethods),
            static::UN_WIRE => $this->gatherPluginsIndexedByMethod($originalClassMethods, $currentClassMethods),
        ];
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
    protected function gatherPluginsIndexedByMethod(array $classMethods, array $classMethodsToCompare): array
    {
        $result = [];

        foreach ($classMethods as $methodName => $classMethod) {
            $extractorStack = $this->createPluginExtractorStack();

            $pluginsFromClass = $this->extractPluginsList($classMethod->stmts[0], $extractorStack);
            $pluginsFromClassToCompare = [];
            if (isset($classMethodsToCompare[$methodName])) {
                $pluginsFromClassToCompare = $this->extractPluginsList($classMethodsToCompare[$methodName]->stmts[0], $extractorStack);
            }

            $pluginsDiff = array_diff($pluginsFromClass, $pluginsFromClassToCompare);

            if ($pluginsDiff) {
                $result[$methodName] = $pluginsDiff;
            }
        }

        return $result;
    }

    /**
     * @return array<\SprykerSdk\Integrator\ManifestGenerator\PluginExtractor\PluginExtractorInterface>
     */
    protected function createPluginExtractorStack(): array
    {
        return [
            new ReturnArrayPluginExtractor(),
            new ReturnClassPluginExtractor(),
            new VariableArrayPluginExtractor(),
        ];
    }

    /**
     * @param \PhpParser\Node\Stmt $statement
     * @param array<\SprykerSdk\Integrator\ManifestGenerator\PluginExtractor\PluginExtractorInterface> $extractors
     *
     * @return array
     */
    protected function extractPluginsList(Stmt $statement, array $extractors): array
    {
        $plugins = [];
        foreach ($extractors as $extractor) {
            if ($extractor->isApplicable($statement)) {
                $plugins = array_merge($extractor->extract($statement->expr), $plugins);
            }
        }

        return $plugins;
    }

    /**
     * @param array<string, array> $pluginChanges
     * @param string $namespace
     * @param array<string, array> $manifests
     *
     * @return array<string, array>
     */
    protected function buildManifestArray(array $pluginChanges, string $namespace, array $manifests): array
    {
        foreach ($pluginChanges as $type => $method) {
            foreach ($method as $methodName => $plugins) {
                foreach ($plugins as $plugin) {
                    [$organization, $module] = $this->getOrganizationAndModuleName($plugin);
                    $manifestKey = $type === static::WIRE ? static::MANIFEST_KEY_WIRE : static::MANIFEST_KEY_UNWIRE;
                    $manifests[$organization . '.' . $module][$manifestKey][] = [
                        static::TARGET => sprintf('%s::%s', $namespace, $methodName),
                        static::SOURCE => $plugin,
                    ];
                }
            }
        }

        return $manifests;
    }
}
