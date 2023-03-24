<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\ConfigReader;

interface ConfigReaderInterface
{
    /**
     * @param string $configPath
     * @param array<string> $configKeys
     *
     * @return array<string, mixed>
     */
    public function read(string $configPath, array $configKeys): array;
}
