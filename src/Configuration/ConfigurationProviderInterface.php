<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Configuration;

interface ConfigurationProviderInterface
{
    /**
     * @return int
     */
    public function getManifestsRatingThreshold(): int;

    /**
     * @return string
     */
    public function getReleaseAppUrl(): string;
}
