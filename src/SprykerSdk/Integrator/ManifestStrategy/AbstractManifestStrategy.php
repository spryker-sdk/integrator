<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerSdk\Integrator\ManifestStrategy;

use SprykerSdk\Integrator\Builder\ClassBuilderFacade;
use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\IntegratorConfig;

abstract class AbstractManifestStrategy implements ManifestStrategyInterface
{
    /**
     * @var \SprykerSdk\Integrator\IntegratorConfig
     */
    protected $config;

    /**
     * @param \SprykerSdk\Integrator\IntegratorConfig $config
     */
    public function __construct(IntegratorConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassBuilderFacade
     */
    protected function getClassBuilderFacade(): ClassBuilderFacade
    {
        return new ClassBuilderFacade();
    }

    /**
     * @param string $question
     * @param array $choices
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $inputOutput
     * @param string|null $defaultValue
     *
     * @return bool|float|int|mixed
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
}
