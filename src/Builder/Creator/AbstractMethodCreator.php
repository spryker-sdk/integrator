<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Creator;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\NodeTraverser;
use SprykerSdk\Integrator\Builder\Visitor\AddUseVisitor;
use SprykerSdk\Integrator\Helper\ClassHelper;
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
     * @var array
     */
    protected const CONST_PREPOSITIONS = [
        self::STATIC_CONST_PREPOSITION,
        self::PARENT_CONST_PREPOSITION,
    ];

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $value
     *
     * @return string
     */
    protected function getShortClassNameAndAddToClassInformation(
        ClassInformationTransfer $classInformationTransfer,
        string $value
    ): string {
        $valueParts = explode('::', $value);
        if (count($valueParts) == static::SIMPLE_VARIABLE_SEMICOLON_COUNT) {
            return $value;
        }

        $classValueParts = explode('\\', $valueParts[0]);
        if (!in_array($valueParts[0], static::CONST_PREPOSITIONS, true)) {
            $nodeTraverser = new NodeTraverser();
            $nodeTraverser->addVisitor(new AddUseVisitor($valueParts[0]));
            $classInformationTransfer->setClassTokenTree(
                $nodeTraverser->traverse($classInformationTransfer->getClassTokenTree()),
            );
        }

        return sprintf('%s::%s', end($classValueParts), $valueParts[1]);
    }

    /**
     * @param string $className
     * @param string $constantName
     *
     * @return \PhpParser\Node\Expr\ClassConstFetch
     */
    protected function createClassConstantExpression(string $className, string $constantName): ClassConstFetch
    {
        return (new BuilderFactory())->classConstFetch(
            (new ClassHelper())->getShortClassName($className),
            $constantName,
        );
    }
}
