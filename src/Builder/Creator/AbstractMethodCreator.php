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
     * @var array
     */
    protected const CONST_PREPOSITIONS = [
        self::STATIC_CONST_PREPOSITION,
        self::PARENT_CONST_PREPOSITION,
    ];

    /**
     * @param string $className
     * @param string $constantName
     *
     * @return \PhpParser\Node\Expr\ClassConstFetch
     */
    protected function createClassConstantExpression(string $className, string $constantName): ClassConstFetch
    {
        return (new BuilderFactory())->classConstFetch($className, $constantName);
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
