<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator\Builder\Visitor\ArgumentBuilder;

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

        $constructorArgumentValues = [];
        if ($classMetadataTransfer->getConstructorArguments()->count()) {
            $constructorArgumentValues = $this->getArguments(
                $classMetadataTransfer->getConstructorArguments()->getArrayCopy(),
            );
        }

        if ($classMetadataTransfer->getPrependArguments()->count()) {
            $prependArgumentValues = $this->getArguments(
                $classMetadataTransfer->getPrependArguments()->getArrayCopy(),
            );

            $args = array_merge($args, $prependArgumentValues);
        }

        $mainArgument = new Arg(
            $this->builderFactory->new(
                (new ClassHelper())->getShortClassName($classMetadataTransfer->getSourceOrFail()),
                $this->builderFactory->args($constructorArgumentValues),
            ),
        );
        $args = array_merge($args, $this->builderFactory->args([$mainArgument]));

        if ($classMetadataTransfer->getAppendArguments()->count()) {
            $appendArgumentValues = $this->getArguments(
                $classMetadataTransfer->getAppendArguments()->getArrayCopy(),
            );

            $args = array_merge($args, $appendArgumentValues);
        }

        return $args;
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
