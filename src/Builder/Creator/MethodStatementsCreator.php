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
     * @return array
     */
    protected function createNodeTreeFromStringValue(ClassInformationTransfer $classInformationTransfer, $value): array
    {
        $arrayItems = [];
        $valueItems = explode(
            '::',
            $this->getShortClassNameAndAddToClassInformation($classInformationTransfer, $value),
        );
        $arrayItems[] = new ArrayItem(
            $this->createClassConstantExpression($valueItems[0], $valueItems[1]),
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

            if (is_string($item)) {
                $item = $this->getShortClassNameAndAddToClassInformation($classInformationTransfer, $item);
                $itemParts = explode('::', $item);
            }
            if (is_bool($item)) {
                $itemParts = [$item];
            }

            if (is_int($key)) {
                $arrayItems[] = $this->createArrayItem($itemParts);

                continue;
            }

            $key = $this->getShortClassNameAndAddToClassInformation($classInformationTransfer, $key);
            $keyParts = explode('::', $key);
            if (is_array($item)) {
                $insideArrayItems = $this->createMethodStatementsFromValue($classInformationTransfer, $item);
                $arrayItems[] = $this->createArrayItem([], $keyParts, $insideArrayItems);

                continue;
            }

            $arrayItems[] = $this->createArrayItem($itemParts, $keyParts);
        }

        return $arrayItems;
    }

    /**
     * @param array $itemParts
     * @param array $keyParts
     * @param array $insideArrayItems
     *
     * @return \PhpParser\Node\Expr\ArrayItem
     */
    protected function createArrayItem(array $itemParts, array $keyParts = [], array $insideArrayItems = []): ArrayItem
    {
        if (count($itemParts) === static::SIMPLE_VARIABLE_SEMICOLON_COUNT) {
            return $this->createSingleSemicolonVariableArrayItem($itemParts, $keyParts);
        }

        if ($insideArrayItems) {
            return new ArrayItem(
                (new BuilderFactory())->val($insideArrayItems),
                $this->createClassConstantExpression($keyParts[static::CONSTANT_TYPE_INDEX], $keyParts[static::CONSTANT_NAME_INDEX]),
            );
        }

        if (!$keyParts) {
            return new ArrayItem(
                $this->createClassConstantExpression($itemParts[static::CONSTANT_TYPE_INDEX], $itemParts[static::CONSTANT_NAME_INDEX]),
            );
        }

        return new ArrayItem(
            $this->createClassConstantExpression($keyParts[static::CONSTANT_TYPE_INDEX], $keyParts[static::CONSTANT_NAME_INDEX]),
            $this->createClassConstantExpression($itemParts[static::CONSTANT_TYPE_INDEX], $itemParts[static::CONSTANT_NAME_INDEX]),
        );
    }

    /**
     * @param array $itemParts
     * @param array $keyParts
     *
     * @return \PhpParser\Node\Expr\ArrayItem
     */
    protected function createSingleSemicolonVariableArrayItem(array $itemParts, array $keyParts): ArrayItem
    {
        if (in_array($itemParts[0], [true, false], true)) {
            return new ArrayItem(
                new ConstFetch(new Name($itemParts[0] ? 'true' : 'false')),
                $this->createClassConstantExpression($keyParts[static::CONSTANT_TYPE_INDEX], $keyParts[static::CONSTANT_NAME_INDEX]),
            );
        }

        $singleItemParts = explode('->', trim($itemParts[0], '$()'));
        if (count($singleItemParts) !== static::SIMPLE_VARIABLE_SEMICOLON_COUNT) {
            return new ArrayItem(
                (new BuilderFactory())->methodCall(
                    new Variable($singleItemParts[static::METHOD_TYPE_INDEX]),
                    new Identifier($singleItemParts[static::METHOD_NAME_INDEX]),
                ),
            );
        }

        return new ArrayItem(
            new String_($itemParts[0]),
            $this->createClassConstantExpression($keyParts[static::CONSTANT_TYPE_INDEX], $keyParts[static::CONSTANT_NAME_INDEX]),
        );
    }
}
