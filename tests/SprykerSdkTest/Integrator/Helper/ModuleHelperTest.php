<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator\Helper;

use PHPUnit\Framework\TestCase;
use SprykerSdk\Integrator\Helper\ModuleHelper;

class ModuleHelperTest extends TestCase
{
    /**
     * @return void
     */
    public function testGetModuleIdShouldReturnValidModuleId(): void
    {
        //Arrange
        $organizationName = 'Spryker';
        $moduleName = 'Acl';
        $version = '1.2.3';

        //Act
        $moduleId = ModuleHelper::getModuleId($organizationName, $moduleName, $version);

        //Assert
        $this->assertSame('Spryker:Acl:1.2.3', $moduleId);
    }
}
