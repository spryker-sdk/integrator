<?php

namespace SprykerSdk\Integrator\ManifestGenerator;

use SprykerSdk\Integrator\ManifestGenerator\Exception\SyntaxTreeException;
use SprykerSdk\Integrator\ManifestGenerator\ValueExtractor\ExtractedValueObject;
use SprykerSdk\Integrator\ManifestGenerator\ValueExtractor\ValueExtractorStrategyCollection;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use PhpParser\ParserFactory;

abstract class AbstractManifestStrategy
{
    /**
     * @var string
     */
    protected const TARGET = 'target';

    /**
     * @var string
     */
    protected const SOURCE = 'source';

    /**
     * @var string
     */
    protected const VALUE = 'value';

    /**
     * @var string
     */
    protected const WIRE = 'wire';

    /**
     * @var string
     */
    protected const UN_WIRE = 'unwire';

    /**
     * @param array<\PhpParser\Node> $syntaxTree
     *
     * @return string
     */
    protected function getNameSpace(array $syntaxTree): string
    {
        $classStatement = $this->getClassStatement($syntaxTree);

        return '\\' . $classStatement->extends->toString();
    }

    /**
     * @param array<\PhpParser\Node> $syntaxTree
     *
     * @throws \SprykerSdk\Integrator\ManifestGenerator\Exception\SyntaxTreeException
     *
     * @return \PhpParser\Node\Stmt\Class_
     */
    protected function getClassStatement(array $syntaxTree): Class_
    {
        /** @var \PhpParser\Node\Stmt\Class_|null $node */
        $node = (new NodeFinder())->findFirst($syntaxTree, function (Node $node) {
            return $node instanceof Class_;
        });

        if (!$node) {
            throw new SyntaxTreeException('Can\'t get class statement from syntax tree');
        }

        return $node;
    }

    /**
     * @return \PhpParser\Parser
     */
    protected function createParser(): Parser
    {
        return (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
    }

    /**
     * @param string $fileName
     *
     * @return array<\PhpParser\Node>
     */
    protected function buildSyntaxTree(string $fileName): array
    {
        if (!file_exists($fileName)) {
            return [];
        }

        $parser = $this->createParser();
        $originalAst = $parser->parse(file_get_contents($fileName));

        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new NameResolver());

        return $nodeTraverser->traverse($originalAst);
    }

    /**
     * @param string $namespace
     *
     * @return array<string>
     */
    protected function getOrganizationAndModuleName(string $namespace): array
    {
        $namespaceParts = explode('\\', $namespace);

        return [$namespaceParts[1], $namespaceParts[3]];
    }

    /**
     * @param array $syntaxTree
     * @param string $methodName
     *
     * @return \PhpParser\Node\Stmt\ClassMethod|null
     */
    protected function findClassMethod(array $syntaxTree, string $methodName): ?ClassMethod
    {
        /** @var \PhpParser\Node\Stmt\ClassMethod|null $node */
        $node = (new NodeFinder())->findFirst($syntaxTree, function (Node $node) use ($methodName) {
            return $node instanceof ClassMethod
                && $node->name->toString() === $methodName;
        });

        return $node;
    }

    /**
     * @param \PhpParser\Node\Expr $expr
     *
     * @return \SprykerSdk\Integrator\ManifestGenerator\ValueExtractor\ExtractedValueObject
     */
    protected function extractValueFromExpression(Expr $expr): ExtractedValueObject
    {
        $valueExtractorCollection = $this->createValueExtractorStrategyCollection();

        return $valueExtractorCollection->execute($expr);
    }

    /**
     * @return \SprykerSdk\Integrator\ManifestGenerator\ValueExtractor\ValueExtractorStrategyCollection
     */
    protected function createValueExtractorStrategyCollection(): ValueExtractorStrategyCollection
    {
        return new ValueExtractorStrategyCollection();
    }
}
