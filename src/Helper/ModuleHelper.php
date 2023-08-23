<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Helper;

class ModuleHelper
{
    /**
     * @param string $organizationName
     * @param string $moduleName
     * @param string $version
     *
     * @return string
     */
    public static function getModuleId(string $organizationName, string $moduleName, string $version): string
    {
        return sprintf('%s:%s:%s', $organizationName, $moduleName, $version);
    }
}
