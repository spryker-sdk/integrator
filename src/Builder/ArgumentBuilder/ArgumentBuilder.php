<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator\Builder\ArgumentBuilder;

use PhpParser\BuilderFactory;
use PhpParser\Node\Arg;
use SprykerSdk\Integrator\Helper\ClassHelper;
use SprykerSdk\Integrator\Transfer\ClassMetadataTransfer;

class ArgumentBuilder implements ArgumentBuilderInterface
{
    /**
     * @var \PhpParser\BuilderFactory
     */
    protected $builderFactory;

    /**
     * @param \PhpParser\BuilderFactory $builderFactory
     */
    public function __construct(BuilderFactory $builderFactory)
    {
        $this->builderFactory = $builderFactory;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $classMetadataTransfer
     *
     * @return array<\PhpParser\Node\Arg>
     */
    public function createAddPluginArguments(ClassMetadataTransfer $classMetadataTransfer): array
    {
        $args = [];
        $args = array_merge($args, $this->getPrependArguments($classMetadataTransfer));
        $args = array_merge($args, $this->getConstructorArguments($classMetadataTransfer));
        $args = array_merge($args, $this->getAppendArguments($classMetadataTransfer));

        return $args;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $classMetadataTransfer
     *
     * @return array<\PhpParser\Node\Arg>
     */
    protected function getConstructorArguments(ClassMetadataTransfer $classMetadataTransfer): array
    {
        $constructorArgumentValues = [];
        if ($classMetadataTransfer->getConstructorArguments()->count()) {
            $constructorArgumentValues = $this->getArguments(
                $classMetadataTransfer->getConstructorArguments()->getArrayCopy(),
            );
        }

        $mainArgument = new Arg(
            $this->builderFactory->new(
                (new ClassHelper())->getShortClassName($classMetadataTransfer->getSourceOrFail()),
                $this->builderFactory->args($constructorArgumentValues),
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
                $args = array_merge($args, $this->builderFactory->args([$classArgumentMetadataTransfer->getValue()]));
            }
        }

        return $args;
    }
}
