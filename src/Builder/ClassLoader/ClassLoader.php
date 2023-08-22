<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\ClassLoader;

use PhpParser\Lexer;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\CloningVisitor;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use SprykerSdk\Integrator\Builder\ComposerClassLoader\ComposerClassLoader;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;

class ClassLoader implements ClassLoaderInterface
{
    /**
    * @var \PhpParser\Parser
    */
    protected $parser;

    /**
     * @var \PhpParser\Lexer
     */
    protected $lexer;

    /**
     * @param \PhpParser\Parser $parser
     * @param \PhpParser\Lexer $lexer
     */
    public function __construct(Parser $parser, Lexer $lexer)
    {
        $this->parser = $parser;
        $this->lexer = $lexer;
    }

    /**
     * @param string $className
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function loadClass(string $className): ClassInformationTransfer
    {
        /** @phpstan-var class-string $className */
        $className = ltrim($className, '\\');

        $classInformationTransfer = (new ClassInformationTransfer())
            ->setClassName($className)
            ->setFullyQualifiedClassName('\\' . $className);

        $fileName = ComposerClassLoader::getFilePath($className);
        if ($fileName === null || !realpath($fileName)) {
            return $classInformationTransfer;
        }
        $fileContents = file_get_contents($fileName);
        if (!$fileContents) {
            return $classInformationTransfer;
        }

        $originalSyntaxTree = $this->parser->parse($fileContents);
        $syntaxTree = $originalSyntaxTree ? $this->traverseOriginalSyntaxTree($originalSyntaxTree) : [];

        $classInformationTransfer->setClassTokenTree($syntaxTree)
            ->setOriginalClassTokenTree($originalSyntaxTree)
            ->setTokens($this->lexer->getTokens())
            ->setFilePath(realpath($fileName));

        $parentClass = $this->getParent($syntaxTree);
        if ($parentClass) {
            $classInformationTransfer->setParent(
                $this->loadClass($parentClass),
            );
        }

        return $classInformationTransfer;
    }

    /**
     * @param array $originalSyntaxTree
     *
     * @return string|null
     */
    protected function getParent(array $originalSyntaxTree): ?string
    {
        $namespace = (new NodeFinder())->findFirst($originalSyntaxTree, function (Node $node) {
            return $node instanceof Namespace_;
        });

        if (!($namespace instanceof Namespace_) || !$namespace->stmts) {
            return null;
        }

        foreach ($namespace->stmts as $stmt) {
            if (!($stmt instanceof Class_)) {
                continue;
            }
            if ($stmt->extends) {
                return $stmt->extends->toString();
            }
        }

        return null;
    }

    /**
     * @param array<\PhpParser\Node\Stmt> $originalSyntaxTree
     *
     * @return array<\PhpParser\Node>
     */
    protected function traverseOriginalSyntaxTree(array $originalSyntaxTree): array
    {
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new CloningVisitor());
        $nodeTraverser->addVisitor(new NameResolver());

        return $nodeTraverser->traverse($originalSyntaxTree);
    }
}
