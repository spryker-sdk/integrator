<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\ClassLoader;

use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\CloningVisitor;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use ReflectionClass;
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
        /** @var class-string $className */
        $className = ltrim($className, '\\');

        $classInformationTransfer = (new ClassInformationTransfer())
            ->setClassName($className)
            ->setFullyQualifiedClassName('\\' . $className);

        $reflectionClass = new ReflectionClass($className);

        $fileName = $reflectionClass->getFileName();
        if (!$fileName) {
            return $classInformationTransfer;
        }
        $fileContents = file_get_contents($fileName);
        if (!$fileContents) {
            return $classInformationTransfer;
        }

        $originalSyntaxTree = $this->parser->parse($fileContents);
        $syntaxTree = $this->traverseOriginalSyntaxTree($originalSyntaxTree);

        $classInformationTransfer->setClassTokenTree($syntaxTree)
            ->setOriginalClassTokenTree($originalSyntaxTree)
            ->setTokens($this->lexer->getTokens())
            ->setFilePath($fileName);

        $parentClass = $reflectionClass->getParentClass();
        if ($parentClass) {
            $classInformationTransfer->setParent(
                $this->loadClass($parentClass->getName()),
            );
        }

        return $classInformationTransfer;
    }

    /**
     * @param array<\PhpParser\Node\Stmt>|null $originalSyntaxTree
     *
     * @return array<\PhpParser\Node>
     */
    protected function traverseOriginalSyntaxTree(?array $originalSyntaxTree): array
    {
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new CloningVisitor());
        $nodeTraverser->addVisitor(new NameResolver());

        return $nodeTraverser->traverse($originalSyntaxTree);
    }
}
