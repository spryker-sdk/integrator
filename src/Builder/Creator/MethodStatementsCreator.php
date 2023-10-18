<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Creator;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;

class MethodStatementsCreator extends AbstractMethodCreator implements MethodStatementsCreatorInterface
{
    /**
     * @var int
     */
    protected const SIMPLE_VARIABLE_SEMICOLON_COUNT = 1;

    /**
     * @var int
     */
    protected const CONSTANT_TYPE_INDEX = 0;

    /**
     * @var int
     */
    protected const CONSTANT_NAME_INDEX = 1;

    /**
     * @var int
     */
    protected const METHOD_TYPE_INDEX = 0;

    /**
     * @var int
     */
    protected const METHOD_NAME_INDEX = 1;

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param mixed $value
     *
     * @return array
     */
    public function createMethodStatementsFromValue(ClassInformationTransfer $classInformationTransfer, $value): array
    {
        if (is_array($value)) {
            return $this->createNodeTreeFromArrayValue($classInformationTransfer, $value);
        }

        return $this->createNodeTreeFromStringValue($classInformationTransfer, $value);
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param mixed $value
     *
     * @return array<mixed>
     */
    protected function createNodeTreeFromStringValue(ClassInformationTransfer $classInformationTransfer, $value): array
    {
        $arrayItems = [];
        $valueItems = explode('::', $value);
        $arrayItems[] = new ArrayItem(
            $this->createClassConstantExpression($classInformationTransfer, $valueItems[0], $valueItems[1]),
        );

        return $arrayItems;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param mixed $value
     *
     * @return array
     */
    protected function createNodeTreeFromArrayValue(ClassInformationTransfer $classInformationTransfer, $value): array
    {
        $arrayItems = [];
        foreach ($value as $key => $item) {
            $itemParts = [];
            $keyParts = [];

            if (!is_int($key)) {
                $keyParts = explode('::', $key);
            }

            if (is_array($item)) {
                $insideArrayItems = $this->createMethodStatementsFromValue($classInformationTransfer, $item);
                $arrayItems[] = $this->createArrayItem($classInformationTransfer, [], $keyParts, $insideArrayItems);

                continue;
            }

            if (is_string($item)) {
                $itemParts = explode('::', $item);
            }

            if (is_bool($item) || is_int($item)) {
                $itemParts = [$item];
            }

            $arrayItems[] = $this->createArrayItem($classInformationTransfer, $itemParts, $keyParts);
        }

        return $arrayItems;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param array $itemParts
     * @param array $keyParts
     * @param array $insideArrayItems
     *
     * @return \PhpParser\Node\Expr\ArrayItem
     */
    protected function createArrayItem(
        ClassInformationTransfer $classInformationTransfer,
        array $itemParts,
        array $keyParts = [],
        array $insideArrayItems = []
    ): ArrayItem {
        if (count($itemParts) === static::SIMPLE_VARIABLE_SEMICOLON_COUNT) {
            return $this->createSingleSemicolonVariableArrayItem($classInformationTransfer, $itemParts, $keyParts);
        }

        if (!$keyParts && !$insideArrayItems) {
            return new ArrayItem($this->createClassConstantExpression($classInformationTransfer, $itemParts[static::CONSTANT_TYPE_INDEX], $itemParts[static::CONSTANT_NAME_INDEX]));
        }
        $countKeyParts = count($keyParts);
        $key = null;

        if ($countKeyParts === 1) {
            $key = $this->createValueExpression($keyParts[static::CONSTANT_TYPE_INDEX]);
        }

        if ($countKeyParts === 2) {
            $key = $this->createClassConstantExpression($classInformationTransfer, $keyParts[static::CONSTANT_TYPE_INDEX], $keyParts[static::CONSTANT_NAME_INDEX]);
        }

        if ($insideArrayItems) {
            return new ArrayItem(
                (new BuilderFactory())->val($insideArrayItems),
                $key,
            );
        }

        return new ArrayItem(
            $this->createClassConstantExpression($classInformationTransfer, $itemParts[static::CONSTANT_TYPE_INDEX], $itemParts[static::CONSTANT_NAME_INDEX]),
            $key,
        );
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param array $itemParts
     * @param array $keyParts
     *
     * @return \PhpParser\Node\Expr\ArrayItem
     */
    protected function createSingleSemicolonVariableArrayItem(ClassInformationTransfer $classInformationTransfer, array $itemParts, array $keyParts): ArrayItem
    {
        if (in_array($itemParts[0], [true, false], true)) {
            return new ArrayItem(
                new ConstFetch(new Name($itemParts[0] ? 'true' : 'false')),
                (count($keyParts) == 1) ?
                    $this->createValueExpression($keyParts[static::CONSTANT_TYPE_INDEX]) :
                    $this->createClassConstantExpression($classInformationTransfer, $keyParts[static::CONSTANT_TYPE_INDEX], $keyParts[static::CONSTANT_NAME_INDEX]),
            );
        }
        $singleItemParts = [];

        if (is_string($itemParts[0])) {
            $singleItemParts = explode('->', trim($itemParts[0], '$()'));
        }
        $countSingleItemParts = count($singleItemParts);
        if ($countSingleItemParts && $countSingleItemParts !== static::SIMPLE_VARIABLE_SEMICOLON_COUNT) {
            return new ArrayItem(
                (new BuilderFactory())->methodCall(
                    new Variable($singleItemParts[static::METHOD_TYPE_INDEX]),
                    new Identifier($singleItemParts[static::METHOD_NAME_INDEX]),
                ),
            );
        }
        $key = null;

        if ($keyParts) {
            $key = (count($keyParts) == 1) ?
                $this->createValueExpression($keyParts[static::CONSTANT_TYPE_INDEX]) :
                $this->createClassConstantExpression($classInformationTransfer, $keyParts[static::CONSTANT_TYPE_INDEX], $keyParts[static::CONSTANT_NAME_INDEX]);
        }

        return new ArrayItem(
            is_string($itemParts[0]) ? new String_($itemParts[0]) : new LNumber($itemParts[0]),
            $key,
        );
    }
}
