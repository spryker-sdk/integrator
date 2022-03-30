<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Client\RabbitMq;

use Spryker\Client\RabbitMq\RabbitMqConfig as SprykerRabbitMqConfig;
use Spryker\Shared\TestIntegrator\TestIntegratorAddArrayElementExisted;

class RabbitMqConfig extends SprykerRabbitMqConfig
{
    protected function getTestConfiguration(): array
    {
        return [
            TestIntegratorAddArrayElementExisted::TEST_INTEGRATION_ADD_ARRAY_ELEMENT_CONST_EXISTED,
        ];
    }
}
