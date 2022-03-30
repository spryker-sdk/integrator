<?php

namespace SprykerSdk\Integrator\ManifestGenerator;

use SprykerSdk\Integrator\ManifestGenerator\Exception\ValueExtractorException;
use PhpParser\Node;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Expression;

class EnvConfigManifestStrategy extends AbstractManifestStrategy implements ManifestStrategyInterface
{
    /**
     * @var string
     */
    public const MANIFEST_KEY = 'configure-env';

    /**
     * @inheritDoc
     */
    public function isApplicable(string $fileName): bool
    {
        return preg_match('#.+/config/Shared/config[a-zA-Z_-]{0,}\.php#', $fileName);
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
     * @return array<array<string, string>>
     */
    protected function compareFiles(array $currentSyntaxTree, array $originalSyntaxTree): array
    {
        $originalExpressions = [];
        if ($originalSyntaxTree) {
            $originalExpressions = $this->getExpressions($originalSyntaxTree);
        }
        $currentExpressions = $this->getExpressions($currentSyntaxTree);

        return $this->compareExpressions($currentExpressions, $originalExpressions);
    }

    /**
     * @param array<\PhpParser\Node> $syntaxTree
     *
     * @return array<string, string>
     */
    protected function getExpressions(array $syntaxTree): array
    {
        $expressions = [];
        /** @var \PhpParser\Node\Stmt\Expression $expression */
        foreach ($syntaxTree as $expression) {
            if (!$this->assertStatement($expression)) {
                continue;
            }

            /** @var \PhpParser\Node\Expr\Assign $assignExpression */
            $assignExpression = $expression->expr;
            /** @var \PhpParser\Node\Expr\ArrayDimFetch $arrayDimFetchExpression */
            $arrayDimFetchExpression = $assignExpression->var;
            /** @var \PhpParser\Node\Expr\ClassConstFetch $constant */
            $constant = $arrayDimFetchExpression->dim;
            $key = sprintf('\%s::%s', $constant->class->toString(), $constant->name->toString());

            try {
                $extractedValueObject = $this->extractValueFromExpression($assignExpression->expr);
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
            $expressions[$key] = $value;
        }

        return $expressions;
    }

    /**
     * @param array $expressions
     * @param array $expressionsToCompare
     *
     * @return array<array<string, string>>
     */
    protected function compareExpressions(array $expressions, array $expressionsToCompare): array
    {
        $diffExpressions = [];
        foreach ($expressions as $key => $value) {
            if (isset($expressionsToCompare[$key]) && $expressionsToCompare[$key] === $value) {
                continue;
            }

            $diffExpressions[] = [
                static::TARGET => $key,
                static::VALUE => $value,
            ];
        }

        return $diffExpressions;
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

    /**
     * @param \PhpParser\Node $statement
     *
     * @return bool
     */
    protected function assertStatement(Node $statement): bool
    {
        return $statement instanceof Expression
            && $statement->expr instanceof Assign
            && $statement->expr->var instanceof ArrayDimFetch
            && $statement->expr->var->dim instanceof ClassConstFetch
            && $statement->expr->var->var instanceof Variable
            && $statement->expr->var->var->name === 'config';
    }
}
