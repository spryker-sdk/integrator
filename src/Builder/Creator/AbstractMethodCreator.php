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
use SprykerSdk\Integrator\Builder\Resolver\PrefixedConstNameResolverInterface;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;

class AbstractMethodCreator
{
    /**
     * @var int
     */
    protected const SIMPLE_VARIABLE_SEMICOLON_COUNT = 1;

    /**
     * @var \SprykerSdk\Integrator\Builder\Resolver\PrefixedConstNameResolverInterface
     */
    protected PrefixedConstNameResolverInterface $prefixedConstNameResolver;

    /**
     * @param \SprykerSdk\Integrator\Builder\Resolver\PrefixedConstNameResolverInterface $prefixedConstNameResolver
     */
    public function __construct(PrefixedConstNameResolverInterface $prefixedConstNameResolver)
    {
        $this->prefixedConstNameResolver = $prefixedConstNameResolver;
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
        return (new BuilderFactory())->classConstFetch(
            $className,
            $this->prefixedConstNameResolver->resolveClassConstantName(
                $classInformationTransfer,
                $className,
                $constantName,
            ),
        );
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
