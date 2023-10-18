<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Creator;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use SprykerSdk\Integrator\Builder\ClassLoader\ClassLoaderInterface;
use SprykerSdk\Integrator\Builder\Finder\ClassConstantFinderInterface;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;

class AbstractMethodCreator
{
    /**
     * @var int
     */
    protected const SIMPLE_VARIABLE_SEMICOLON_COUNT = 1;

    /**
     * @var string
     */
    protected const STATIC_CONST_PREPOSITION = 'static';

    /**
     * @var string
     */
    protected const PARENT_CONST_PREPOSITION = 'parent';

    /**
     * @var string
     */
    protected const PYZ_CONST_PREFIX = 'PYZ_';

    /**
     * @var array
     */
    protected const CONST_PREPOSITIONS = [
        self::STATIC_CONST_PREPOSITION,
        self::PARENT_CONST_PREPOSITION,
    ];

    /**
     * @var \SprykerSdk\Integrator\Builder\Finder\ClassConstantFinderInterface
     */
    protected ClassConstantFinderInterface $classConstantFinder;

    /**
     * @var \SprykerSdk\Integrator\Builder\ClassLoader\ClassLoaderInterface
     */
    protected ClassLoaderInterface $classLoader;

    /**
     * @param \SprykerSdk\Integrator\Builder\Finder\ClassConstantFinderInterface $classConstantFinder
     * @param \SprykerSdk\Integrator\Builder\ClassLoader\ClassLoaderInterface $classLoader
     */
    public function __construct(ClassConstantFinderInterface $classConstantFinder, ClassLoaderInterface $classLoader)
    {
        $this->classConstantFinder = $classConstantFinder;
        $this->classLoader = $classLoader;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $className
     * @param string $constantName
     *
     * @return \PhpParser\Node\Expr\ClassConstFetch
     */
    protected function createClassConstantExpression(
        ClassInformationTransfer $classInformationTransfer,
        string $className,
        string $constantName
    ): ClassConstFetch {
        return (new BuilderFactory())->classConstFetch($className, $this->getClassConstantName($classInformationTransfer, $className, $constantName));
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $className
     * @param string $constantName
     *
     * @return string
     */
    protected function getClassConstantName(ClassInformationTransfer $classInformationTransfer, string $className, string $constantName): string
    {
        $targetClassName = $className;

        if (in_array($className, static::CONST_PREPOSITIONS, true)) {
            $targetClassName = $classInformationTransfer->getClassName();

            if ($targetClassName === null) {
                return $constantName;
            }
        }

        if (!$this->classLoader->classExist($targetClassName)) {
            return $constantName;
        }

        $targetClass = $this->classLoader->loadClass($targetClassName);

        $prefixedConstantName = static::PYZ_CONST_PREFIX . $constantName;

        return $this->classConstantFinder->findConstantByName($targetClass, $prefixedConstantName) ? $prefixedConstantName : $constantName;
    }

    /**
     * @param string $value
     *
     * @return \PhpParser\Node\Expr
     */
    protected function createValueExpression(string $value): Expr
    {
        return (new BuilderFactory())->val($value);
    }
}
