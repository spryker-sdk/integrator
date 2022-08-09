<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator\Builder\ClassMetadataBuilder\Plugin;

use ArrayObject;
use SprykerSdk\Integrator\IntegratorConfig;
use SprykerSdk\Integrator\Transfer\ClassArgumentMetadataTransfer;
use SprykerSdk\Integrator\Transfer\ClassMetadataTransfer;

class ArgumentBuilderPlugin implements ClassMetadataBuilderPluginInterface
{
    /**
     * @param array<mixed> $manifest
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $transfer
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer
     */
    public function build(array $manifest, ClassMetadataTransfer $transfer): ClassMetadataTransfer
    {
        if (isset($manifest[IntegratorConfig::MANIFEST_KEY_ARGUMENTS])) {
            if (isset($manifest[IntegratorConfig::MANIFEST_KEY_ARGUMENTS][IntegratorConfig::MANIFEST_KEY_ARGUMENTS_PREPEND])) {
                $prependArguments = new ArrayObject();
                foreach ($manifest[IntegratorConfig::MANIFEST_KEY_ARGUMENTS][IntegratorConfig::MANIFEST_KEY_ARGUMENTS_PREPEND] as $argumentData) {
                    $prependArguments->append($this->createClassArgumentMetadataTransfer($argumentData));
                }
                $transfer->setPrependArguments($prependArguments);
            }

            if (isset($manifest[IntegratorConfig::MANIFEST_KEY_ARGUMENTS][IntegratorConfig::MANIFEST_KEY_ARGUMENTS_APPEND])) {
                $appendArguments = new ArrayObject();
                foreach ($manifest[IntegratorConfig::MANIFEST_KEY_ARGUMENTS][IntegratorConfig::MANIFEST_KEY_ARGUMENTS_APPEND] as $argumentData) {
                    $appendArguments->append($this->createClassArgumentMetadataTransfer($argumentData));
                }
                $transfer->setAppendArguments($appendArguments);
            }

            if (isset($manifest[IntegratorConfig::MANIFEST_KEY_ARGUMENTS][IntegratorConfig::MANIFEST_KEY_ARGUMENTS_CONSTRUCTOR])) {
                $constructorArguments = new ArrayObject();
                foreach ($manifest[IntegratorConfig::MANIFEST_KEY_ARGUMENTS][IntegratorConfig::MANIFEST_KEY_ARGUMENTS_CONSTRUCTOR] as $argumentData) {
                    $constructorArguments->append($this->createClassArgumentMetadataTransfer($argumentData));
                }
                $transfer->setConstructorArguments($constructorArguments);
            }
        }

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
}
