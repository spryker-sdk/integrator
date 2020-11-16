<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerSdk\Zed\Integrator\Business\ManifestStrategy;

use SprykerSdk\Zed\Integrator\Business\Builder\ClassBuilderFacade;
use SprykerSdk\Zed\Integrator\Dependency\Console\IOInterface;
use SprykerSdk\Zed\Integrator\IntegratorConfig;

abstract class AbstractManifestStrategy implements ManifestStrategyInterface
{
    /**
     * @var \SprykerSdk\Zed\Integrator\IntegratorConfig
     */
    protected $config;

    /**
     * @param \SprykerSdk\Zed\Integrator\IntegratorConfig $config
     */
    public function __construct(IntegratorConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @return \SprykerSdk\Zed\Integrator\Business\Builder\ClassBuilderFacade
     */
    protected function getClassBuilderFacade(): ClassBuilderFacade
    {
        return new ClassBuilderFacade();
    }

    /**
     * @param string $question
     * @param array $choices
     * @param \SprykerSdk\Zed\Integrator\Dependency\Console\IOInterface $inputOutput
     * @param string|null $defaultValue
     *
     * @return bool|float|int|mixed
     */
    protected function askValue(string $question, array $choices, IOInterface $inputOutput, ?string $defaultValue)
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
