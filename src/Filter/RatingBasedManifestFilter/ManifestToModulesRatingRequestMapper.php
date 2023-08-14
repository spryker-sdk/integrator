<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Filter\RatingBasedManifestFilter;

use SprykerSdk\Integrator\Communication\ReleaseApp\ModuleRatingRequestDto;
use SprykerSdk\Integrator\Communication\ReleaseApp\ModulesRatingRequestDto;
use SprykerSdk\Integrator\IntegratorConfig;

class ManifestToModulesRatingRequestMapper
{
    /**
     * @param array $manifests
     *
     * @return \SprykerSdk\Integrator\Communication\ReleaseApp\ModulesRatingRequestDto
     */
    public function mapManifestsToModulesRatingRequest(array $manifests): ModulesRatingRequestDto
    {
        $moduleRatingRequestDtos = [];

        foreach ($manifests as $strategies) {
            foreach ($strategies as $strategy) {
                foreach ($strategy as $manifest) {
                    [$organization, $moduleName] = explode('.', $manifest[IntegratorConfig::MODULE_KEY]);
                    $moduleVersionName = $manifest[IntegratorConfig::MODULE_VERSION_KEY];
                    $moduleRatingRequestDtos[] = new ModuleRatingRequestDto($organization, $moduleName, $moduleVersionName);
                }
            }
        }

        return new ModulesRatingRequestDto($moduleRatingRequestDtos);
    }
}
