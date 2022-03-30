<?php

namespace SprykerSdk\Integrator\ManifestGenerator;

use SprykerSdk\Integrator\ManifestGenerator\Exception\SyntaxTreeException;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;

class WidgetsManifestStrategy extends AbstractManifestStrategy implements ManifestStrategyInterface
{
    /**
     * @var string
     */
    public const MANIFEST_KEY_WIRE = 'wire-widget';

    /**
     * @var string
     */
    public const MANIFEST_KEY_UNWIRE = 'unwire-widget';

    /**
     * @var string
     */
    protected const WIDGET_METHOD_NAME = 'getGlobalWidgets';

    /**
     * @param string $fileName
     *
     * @return bool
     */
    public function isApplicable(string $fileName): bool
    {
        return strpos($fileName, 'ShopApplicationDependencyProvider.php') !== false;
    }

    /**
     * @param string $fileName
     * @param string $originalFileName
     * @param array<string, array> $existingChanges
     *
     * @return array<string, array>
     */
    public function generateManifestData(string $fileName, string $originalFileName, array $existingChanges = []): array
    {
        $syntaxTree = $this->buildSyntaxTree($fileName);
        $originalSyntaxTree = [];

        if (file_exists($originalFileName)) {
            $originalSyntaxTree = $this->buildSyntaxTree($originalFileName);
        }

        $data = $this->compareFiles($syntaxTree, $originalSyntaxTree);

        return $this->buildManifestArray($data, $existingChanges);
    }

    /**
     * @param array<\PhpParser\Node> $currentSyntaxTree
     * @param array<\PhpParser\Node> $originalSyntaxTree
     *
     * @throws \SprykerSdk\Integrator\ManifestGenerator\Exception\SyntaxTreeException
     *
     * @return array<string, array>
     */
    protected function compareFiles(array $currentSyntaxTree, array $originalSyntaxTree): array
    {
        $originalRelationData = [];
        if ($originalSyntaxTree) {
            $originalMethod = $this->findClassMethod($originalSyntaxTree, static::WIDGET_METHOD_NAME);
            if ($originalMethod) {
                $originalRelationData = $this->extractWidgetList($originalMethod);
            }
        }

        $currentMethod = $this->findClassMethod($currentSyntaxTree, static::WIDGET_METHOD_NAME);
        if (!$currentMethod) {
            throw new SyntaxTreeException(sprintf('Can\'t find a %s method in the ShopApplicationDependencyProvider', static::WIDGET_METHOD_NAME));
        }
        $currentRelationData = $this->extractWidgetList($currentMethod);

        return [
            static::WIRE => $this->gatherWidgets($currentRelationData, $originalRelationData),
            static::UN_WIRE => $this->gatherWidgets($originalRelationData, $currentRelationData),
        ];
    }

    /**
     * @param array<string> $widgets
     * @param array<string> $widgetsToCompare
     *
     * @return array<string>
     */
    protected function gatherWidgets(array $widgets, array $widgetsToCompare): array
    {
        return array_diff($widgets, $widgetsToCompare);
    }

    /**
     * @param \PhpParser\Node\Stmt\ClassMethod $classMethod
     *
     * @return array<string>
     */
    protected function extractWidgetList(ClassMethod $classMethod): array
    {
        $data = [];
        if (!($classMethod->stmts[0] instanceof Return_) || (!$classMethod->stmts[0]->expr instanceof Array_)) {
            return [];
        }

        foreach ($classMethod->stmts[0]->expr->items as $item) {
            if ($item->value instanceof ClassConstFetch && $item->value->name->name === 'class') {
                $data[] = '\\' . $item->value->class->toString();
            }
        }

        return $data;
    }

    /**
     * @param array<string, array> $data
     * @param array<string, array> $manifests
     *
     * @return array<string, array>
     */
    protected function buildManifestArray(array $data, array $manifests): array
    {
        foreach ($data as $type => $changes) {
            foreach ($changes as $record) {
                [$organization, $module] = $this->getOrganizationAndModuleName($record);
                $manifestKey = $type === static::WIRE ? static::MANIFEST_KEY_WIRE : static::MANIFEST_KEY_UNWIRE;
                $manifests[$organization . '.' . $module][$manifestKey][] = [
                    static::SOURCE => $record,
                ];
            }
        }

        return $manifests;
    }
}
