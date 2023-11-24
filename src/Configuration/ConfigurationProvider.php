<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Configuration;

/**
 * @codeCoverageIgnore
 */
class ConfigurationProvider implements ConfigurationProviderInterface
{
    /**
     * @var int
     */
    protected const DEFAULT_MANIFESTS_RATING_THRESHOLD = 0;

    /**
     * @var string
     */
    protected const DEFAULT_RELEASE_APP_URL = 'https://api.release.spryker.com';

    /**
     * @return int
     */
    public function getManifestsRatingThreshold(): int
    {
        return (int)getenv('MANIFESTS_RATING_THRESHOLD') ?: static::DEFAULT_MANIFESTS_RATING_THRESHOLD;
    }

    /**
     * @return string
     */
    public function getReleaseAppUrl(): string
    {
        return (string)getenv('RELEASE_APP_URL') ?: static::DEFAULT_RELEASE_APP_URL;
    }
}
