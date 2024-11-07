<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\ClassLoader;

use Composer\Autoload\ClassLoader as ComposerClassLoader;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeFinder;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;

class ClassLoader extends AbstractLoader implements ClassLoaderInterface
{
    /**
     * @var \Composer\Autoload\ClassLoader|null
     */
    private static ?ComposerClassLoader $composerClassLoader = null;

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

        $fileName = $this->getFilePath($className);
        if ($fileName === null || !realpath($fileName)) {
            return $classInformationTransfer;
        }
        $fileContents = file_get_contents($fileName);
        if (!$fileContents) {
            return $classInformationTransfer;
        }

        $originalSyntaxTree = $this->parser->parse($fileContents);
        $syntaxTree = $originalSyntaxTree ? $this->traverseOriginalSyntaxTree($originalSyntaxTree) : [];

        $classInformationTransfer->setTokenTree($syntaxTree)
            ->setOriginalTokenTree($originalSyntaxTree)
            ->setTokens($this->parser->getTokens())
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
     * @param string $className
     *
     * @return bool
     */
    public function classExist(string $className): bool
    {
        return (bool)$this->getFilePath($className);
    }

    /**
     * @param string $className
     *
     * @return string|null
     */
    protected function getFilePath(string $className): ?string
    {
        return $this->getComposerClassLoader()->findFile(ltrim($className, '\\')) ?: null;
    }

    /**
     * @phpcsSuppress Spryker.Internal.SprykerPreferStaticOverSelf.StaticVsSelf
     *
     * @return \Composer\Autoload\ClassLoader
     */
    protected function getComposerClassLoader(): ComposerClassLoader
    {
        if (static::$composerClassLoader === null) {
            if (file_exists(APPLICATION_ROOT_DIR . '/vendor/autoload.php')) {
                static::$composerClassLoader = require APPLICATION_ROOT_DIR . '/vendor/autoload.php';
                static::$composerClassLoader->unregister();

                return static::$composerClassLoader;
            }

            static::$composerClassLoader = require INTEGRATOR_ROOT_DIR . '/vendor/autoload.php';
        }

        return static::$composerClassLoader;
    }
}
