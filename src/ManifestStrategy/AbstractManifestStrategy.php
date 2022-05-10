<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\ManifestStrategy;

use ArrayObject;
use SprykerSdk\Integrator\Builder\ClassBuilderFacade;
use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\Helper\ClassHelperInterface;
use SprykerSdk\Integrator\IntegratorConfig;
use SprykerSdk\Integrator\Transfer\ClassArgumentMetadataTransfer;
use SprykerSdk\Integrator\Transfer\ClassMetadataTransfer;

abstract class AbstractManifestStrategy implements ManifestStrategyInterface
{
    /**
     * @var \SprykerSdk\Integrator\IntegratorConfig
     */
    protected $config;

    /**
     * @var \SprykerSdk\Integrator\Helper\ClassHelperInterface
     */
    protected $classHelper;

    /**
     * @param \SprykerSdk\Integrator\IntegratorConfig $config
     * @param \SprykerSdk\Integrator\Helper\ClassHelperInterface $classHelper
     */
    public function __construct(IntegratorConfig $config, ClassHelperInterface $classHelper)
    {
        $this->config = $config;
        $this->classHelper = $classHelper;
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassBuilderFacade
     */
    protected function createClassBuilderFacade(): ClassBuilderFacade
    {
        return new ClassBuilderFacade();
    }

    /**
     * @param string $question
     * @param array $choices
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $inputOutput
     * @param string|null $defaultValue
     *
     * @return mixed|float|int|bool
     */
    protected function askValue(string $question, array $choices, InputOutputInterface $inputOutput, ?string $defaultValue)
    {
        if ($choices) {
            $value = $inputOutput->choice($question, $choices, (string)$defaultValue);
        } else {
            $value = $inputOutput->ask($question, (string)$defaultValue);
        }

        if (is_numeric($value)) {
            if ($value == (float)$value) {
                $value = (float)$value;
            } else {
                $value = (int)$value;
            }
        } elseif ($value === 'false') {
            $value = false;
        } elseif ($value === 'true') {
            $value = true;
        }

        return $value;
    }

    /**
     * @param array $manifest
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer
     */
    protected function createClassMetadataTransfer(array $manifest): ClassMetadataTransfer
    {
        $transfer = (new ClassMetadataTransfer())
            ->setSource(ltrim($manifest[IntegratorConfig::MANIFEST_KEY_SOURCE], '\\'))
            ->setTarget(ltrim($manifest[IntegratorConfig::MANIFEST_KEY_TARGET], '\\'));

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

        $transfer->setBefore(ltrim($manifest[IntegratorConfig::MANIFEST_KEY_POSITION][IntegratorConfig::MANIFEST_KEY_POSITION_BEFORE] ?? '', '\\'));
        $transfer->setAfter(ltrim($manifest[IntegratorConfig::MANIFEST_KEY_POSITION][IntegratorConfig::MANIFEST_KEY_POSITION_AFTER] ?? '', '\\'));

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
