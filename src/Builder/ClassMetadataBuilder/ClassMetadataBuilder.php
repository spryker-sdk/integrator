<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator\Builder\ClassMetadataBuilder;

use ArrayObject;
use SprykerSdk\Integrator\IntegratorConfig;
use SprykerSdk\Integrator\Transfer\ClassArgumentMetadataTransfer;
use SprykerSdk\Integrator\Transfer\ClassMetadataTransfer;

class ClassMetadataBuilder implements ClassMetadataBuilderInterface
{
    /**
     * @param array<mixed> $manifest
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer
     */
    public function build(array $manifest): ClassMetadataTransfer
    {
        $transfer = new ClassMetadataTransfer();

        if (isset($manifest[IntegratorConfig::MANIFEST_KEY_ARGUMENTS])) {
            $this->addPrependArguments($manifest, $transfer);
            $this->addConstructorArguments($manifest, $transfer);
            $this->addAppendArguments($manifest, $transfer);
        }

        $this->addIndex($manifest, $transfer);
        $this->addPositions($manifest, $transfer);
        $this->addSourceAndTarget($manifest, $transfer);

        return $transfer;
    }

    /**
     * @param array<mixed> $manifest
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $transfer
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer
     */
    protected function addConstructorArguments(array $manifest, ClassMetadataTransfer $transfer): ClassMetadataTransfer
    {
        if (!isset($manifest[IntegratorConfig::MANIFEST_KEY_ARGUMENTS][IntegratorConfig::MANIFEST_KEY_ARGUMENTS_CONSTRUCTOR])) {
            return $transfer;
        }

        $constructorArguments = new ArrayObject();
        foreach ($manifest[IntegratorConfig::MANIFEST_KEY_ARGUMENTS][IntegratorConfig::MANIFEST_KEY_ARGUMENTS_CONSTRUCTOR] as $argumentData) {
            $constructorArguments->append($this->createClassArgumentMetadataTransfer($argumentData));
        }
        $transfer->setConstructorArguments($constructorArguments);

        return $transfer;
    }

    /**
     * @param array<mixed> $manifest
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $transfer
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer
     */
    protected function addPrependArguments(array $manifest, ClassMetadataTransfer $transfer): ClassMetadataTransfer
    {
        if (!isset($manifest[IntegratorConfig::MANIFEST_KEY_ARGUMENTS][IntegratorConfig::MANIFEST_KEY_ARGUMENTS_PREPEND])) {
            return $transfer;
        }

        $prependArguments = new ArrayObject();
        foreach ($manifest[IntegratorConfig::MANIFEST_KEY_ARGUMENTS][IntegratorConfig::MANIFEST_KEY_ARGUMENTS_PREPEND] as $argumentData) {
            $prependArguments->append($this->createClassArgumentMetadataTransfer($argumentData));
        }
        $transfer->setPrependArguments($prependArguments);

        return $transfer;
    }

    /**
     * @param array<mixed> $manifest
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $transfer
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer
     */
    protected function addAppendArguments(array $manifest, ClassMetadataTransfer $transfer): ClassMetadataTransfer
    {
        if (!isset($manifest[IntegratorConfig::MANIFEST_KEY_ARGUMENTS][IntegratorConfig::MANIFEST_KEY_ARGUMENTS_APPEND])) {
            return $transfer;
        }

        $appendArguments = new ArrayObject();
        foreach ($manifest[IntegratorConfig::MANIFEST_KEY_ARGUMENTS][IntegratorConfig::MANIFEST_KEY_ARGUMENTS_APPEND] as $argumentData) {
            $appendArguments->append($this->createClassArgumentMetadataTransfer($argumentData));
        }
        $transfer->setAppendArguments($appendArguments);

        return $transfer;
    }

    /**
     * @param array<string> $argumentData
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassArgumentMetadataTransfer
     */
    protected function createClassArgumentMetadataTransfer(array $argumentData): ClassArgumentMetadataTransfer
    {
        return (new ClassArgumentMetadataTransfer())
            ->setValue($argumentData['value'])
            ->setIsLiteral((bool)$argumentData['is_literal']);
    }

    /**
     * @param array<mixed> $manifest
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $transfer
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer
     */
    protected function addIndex(array $manifest, ClassMetadataTransfer $transfer): ClassMetadataTransfer
    {
        $transfer->setIndex($manifest[IntegratorConfig::MANIFEST_KEY_INDEX] ?? null);

        return $transfer;
    }

    /**
     * @param array<mixed> $manifest
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $transfer
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer
     */
    protected function addSourceAndTarget(array $manifest, ClassMetadataTransfer $transfer): ClassMetadataTransfer
    {
        $targets = explode('::', $manifest[IntegratorConfig::MANIFEST_KEY_TARGET]);

        $transfer->setSource(ltrim($manifest[IntegratorConfig::MANIFEST_KEY_SOURCE], '\\'));
        $transfer->setTarget(ltrim($manifest[IntegratorConfig::MANIFEST_KEY_TARGET], '\\'));
        $transfer->setTargetMethodName(end($targets));

        return $transfer;
    }

    /**
     * @param array<mixed> $manifest
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $transfer
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer
     */
    protected function addPositions(array $manifest, ClassMetadataTransfer $transfer): ClassMetadataTransfer
    {
        $transfer->setBefore(ltrim($manifest[IntegratorConfig::MANIFEST_KEY_POSITION][IntegratorConfig::MANIFEST_KEY_POSITION_BEFORE] ?? '', '\\'));
        $transfer->setAfter(ltrim($manifest[IntegratorConfig::MANIFEST_KEY_POSITION][IntegratorConfig::MANIFEST_KEY_POSITION_AFTER] ?? '', '\\'));
        $transfer->setCondition($manifest[IntegratorConfig::MANIFEST_KEY_CONDITION] ?? null);

        return $transfer;
    }
}
