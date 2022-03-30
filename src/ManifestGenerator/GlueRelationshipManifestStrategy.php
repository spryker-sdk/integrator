<?php

namespace SprykerSdk\Integrator\ManifestGenerator;

use SprykerSdk\Integrator\ManifestGenerator\Exception\SyntaxTreeException;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\Expression;

class GlueRelationshipManifestStrategy extends AbstractManifestStrategy implements ManifestStrategyInterface
{
    /**
     * @var string
     */
    public const MANIFEST_KEY_WIRE = 'wire-glue-relationship';

    /**
     * @var string
     */
    public const MANIFEST_KEY_UNWIRE = 'unwire-glue-relationship';

    /**
     * @var string
     */
    protected const RELATIONSHIP_METHOD_NAME = 'getResourceRelationshipPlugins';

    /**
     * @var string
     */
    protected const DATA_KEY_CONFIG = 'config';

    /**
     * @var string
     */
    protected const DATA_KEY_PLUGIN = 'plugin';

    /**
     * @inheritDoc
     */
    public function isApplicable(string $fileName): bool
    {
        return strpos($fileName, 'GlueApplicationDependencyProvider.php') !== false;
    }

    /**
     * @inheritDoc
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
            $originalMethod = $this->findClassMethod($originalSyntaxTree, static::RELATIONSHIP_METHOD_NAME);
            if ($originalMethod) {
                $originalRelationData = $this->extractRelationList($originalMethod->stmts);
            }
        }

        $currentMethod = $this->findClassMethod($currentSyntaxTree, static::RELATIONSHIP_METHOD_NAME);
        if (!$currentMethod) {
            throw new SyntaxTreeException(sprintf('Can\'t find %s method in the GlueApplicationDependencyProvider class', static::RELATIONSHIP_METHOD_NAME));
        }
        $currentRelationData = $this->extractRelationList($currentMethod->stmts);

        return [
            static::WIRE => $this->gatherRelations($currentRelationData, $originalRelationData),
            static::UN_WIRE => $this->gatherRelations($originalRelationData, $currentRelationData),

        ];
    }

    /**
     * @param array $relationData
     * @param array $relationDataToCompare
     *
     * @return array
     */
    protected function gatherRelations(array $relationData, array $relationDataToCompare): array
    {
        $result = [];
        foreach ($relationData as $relationKey => $relationPlugins) {
            $plugins = !isset($relationDataToCompare[$relationKey]) ? $relationPlugins : array_diff($relationPlugins, $relationDataToCompare[$relationKey]);
            if (!$plugins) {
                continue;
            }
            foreach ($plugins as $plugin) {
                $result[] = [static::DATA_KEY_CONFIG => $relationKey, static::DATA_KEY_PLUGIN => $plugin];
            }
        }

        return $result;
    }

    /**
     * @param array<\PhpParser\Node\Stmt> $expressions
     *
     * @return array
     */
    protected function extractRelationList(array $expressions): array
    {
        $data = [];
        foreach ($expressions as $expression) {
            if (!($expression instanceof Expression) || !($expression->expr instanceof MethodCall) || $expression->expr->name->name !== 'addRelationship') {
                continue;
            }

            /** @var \PhpParser\Node\Expr\ClassConstFetch $constantExpression */
            $constantExpression = $expression->expr->args[0]->value;
            /** @var \PhpParser\Node\Expr\New_ $pluginExpression */
            $pluginExpression = $expression->expr->args[1]->value;
            $constName = sprintf('\\%s::%s', $constantExpression->class->toString(), $constantExpression->name->toString());
            $data[$constName][] = '\\' . $pluginExpression->class->toString();
        }

        return $data;
    }

    /**
     * @param array<string, array<string, array>> $data
     * @param array<string, array> $manifests
     *
     * @return array<string, array>
     */
    protected function buildManifestArray(array $data, array $manifests): array
    {
        foreach ($data as $type => $changes) {
            foreach ($changes as $record) {
                [$organization, $module] = $this->getOrganizationAndModuleName($record[static::DATA_KEY_PLUGIN]);
                $manifestKey = $type === static::WIRE ? static::MANIFEST_KEY_WIRE : static::MANIFEST_KEY_UNWIRE;
                $manifests[$organization . '.' . $module][$manifestKey][] = [
                    static::SOURCE => [
                        $record[static::DATA_KEY_CONFIG] => $record[static::DATA_KEY_PLUGIN],
                    ],
                ];
            }
        }

        return $manifests;
    }
}
