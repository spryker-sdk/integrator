<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Composer;

interface ComposerLockReaderInterface
{
    /**
     * @return array<string>
     */
    public function getModuleVersions(): array;

    /**
     * @param string $packageName
     *
     * @return array|null
     */
    public function getPackageData(string $packageName): ?array;
}
