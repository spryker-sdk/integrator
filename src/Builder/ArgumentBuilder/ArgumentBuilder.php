<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\ArgumentBuilder;

use ArrayObject;
use PhpParser\BuilderFactory;
use PhpParser\Node\Arg;
use SprykerSdk\Integrator\Builder\PartialParser\ExpressionPartialParserInterface;
use SprykerSdk\Integrator\Helper\ClassHelper;
use SprykerSdk\Integrator\Transfer\ClassMetadataTransfer;

class ArgumentBuilder implements ArgumentBuilderInterface
{
    /**
     * @var \PhpParser\BuilderFactory
     */
    protected $builderFactory;

    /**
     * @var \SprykerSdk\Integrator\Builder\PartialParser\ExpressionPartialParserInterface
     */
    protected ExpressionPartialParserInterface $expressionPartialParser;

    /**
     * @param \PhpParser\BuilderFactory $builderFactory
     * @param \SprykerSdk\Integrator\Builder\PartialParser\ExpressionPartialParserInterface $expressionPartialParser
     */
    public function __construct(BuilderFactory $builderFactory, ExpressionPartialParserInterface $expressionPartialParser)
    {
        $this->builderFactory = $builderFactory;
        $this->expressionPartialParser = $expressionPartialParser;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $classMetadataTransfer
     * @param bool $withSource
     *
     * @return array<\PhpParser\Node\Arg>
     */
    public function createAddPluginArguments(ClassMetadataTransfer $classMetadataTransfer, bool $withSource = true): array
    {
        return array_merge(
            $this->getPrependArguments($classMetadataTransfer),
            $this->getConstructorArguments($classMetadataTransfer, $withSource),
            $this->getAppendArguments($classMetadataTransfer),
        );
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $classMetadataTransfer
     * @param bool $withSource
     *
     * @return array<\PhpParser\Node\Arg>
     */
    protected function getConstructorArguments(ClassMetadataTransfer $classMetadataTransfer, bool $withSource = true): array
    {
        $constructorArgumentValues = [];
        if ($classMetadataTransfer->getConstructorArguments()->count()) {
            $constructorArgumentValues = $this->getArguments(
                $classMetadataTransfer->getConstructorArguments()->getArrayCopy(),
            );
        }

        $args = $this->builderFactory->args($constructorArgumentValues);

        if (!$withSource) {
            return $args;
        }
        $mainArgument = new Arg(
            $this->builderFactory->new(
                (new ClassHelper())->getShortClassName($classMetadataTransfer->getSourceOrFail()),
                $args,
            ),
        );

        return $this->builderFactory->args([$mainArgument]);
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $classMetadataTransfer
     *
     * @return array<\PhpParser\Node\Arg>
     */
    protected function getAppendArguments(ClassMetadataTransfer $classMetadataTransfer): array
    {
        if (!$classMetadataTransfer->getAppendArguments()->count()) {
            return [];
        }

        $appendArgumentValues = $this->getArguments(
            $classMetadataTransfer->getAppendArguments()->getArrayCopy(),
        );

        return $appendArgumentValues;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $classMetadataTransfer
     *
     * @return array<\PhpParser\Node\Arg>
     */
    protected function getPrependArguments(ClassMetadataTransfer $classMetadataTransfer): array
    {
        if (!$classMetadataTransfer->getPrependArguments()->count()) {
            return [];
        }

        $prependArgumentValues = $this->getArguments(
            $classMetadataTransfer->getPrependArguments()->getArrayCopy(),
        );

        return $prependArgumentValues;
    }

    /**
     * @param array<int, \SprykerSdk\Integrator\Transfer\ClassArgumentMetadataTransfer> $classArgumentMetadataTransfers
     *
     * @return array<\PhpParser\Node\Arg>
     */
    protected function getArguments(array $classArgumentMetadataTransfers): array
    {
        $args = [];
        foreach ($classArgumentMetadataTransfers as $classArgumentMetadataTransfer) {
            if ($classArgumentMetadataTransfer->getIsLiteral()) {
                $metadataValue = json_decode((string)$classArgumentMetadataTransfer->getValue());
                if (is_string($metadataValue)) {
                    $metadataValue = $this->expressionPartialParser->parse($metadataValue)->getExpression()->expr;
                }

                $args = array_merge($args, $this->builderFactory->args([$metadataValue]));
            }
        }

        return $args;
    }

    /**
     * @param \ArrayObject<int, \SprykerSdk\Integrator\Transfer\ClassArgumentMetadataTransfer> $classArgumentMetadataTransfers
     *
     * @return array<string>
     */
    public function getValueArguments(ArrayObject $classArgumentMetadataTransfers): array
    {
        $args = [];
        foreach ($classArgumentMetadataTransfers as $classArgumentMetadataTransfer) {
            if ($classArgumentMetadataTransfer->getIsLiteral()) {
                $metadataValue = json_decode((string)$classArgumentMetadataTransfer->getValue());

                $args = array_merge($args, is_array($metadataValue) ? $metadataValue : [$metadataValue]);
            }
        }

        return $args;
    }
}
