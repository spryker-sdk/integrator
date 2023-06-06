<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator\ConfigReader;

use PhpParser\ParserFactory;
use SprykerSdk\Integrator\ConfigReader\ConfigReader;
use SprykerSdkTest\Integrator\BaseTestCase;

class ConfigReaderTest extends BaseTestCase
{
    /**
     * @return void
     */
    public function testReadShouldReturnParsedValues(): void
    {
        // Arrange
        $configFilePath = $this->getDataDirectoryPath() . '/project_config/config_default.php';
        $configReader = new ConfigReader(new ParserFactory());
        $configKeys = ['KernelConstants::PROJECT_NAMESPACES', 'KernelConstants::CORE_NAMESPACES'];

        // Act
        $result = $configReader->read($configFilePath, $configKeys);

        // Assert
        $this->assertSame([
            'KernelConstants::PROJECT_NAMESPACES' => ['Pyz'],
            'KernelConstants::CORE_NAMESPACES' => [
                'SprykerShop',
                'SprykerEco',
                'Spryker',
                'SprykerSdk',
            ],
        ], $result);
    }
}
