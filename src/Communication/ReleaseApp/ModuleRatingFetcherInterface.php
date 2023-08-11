<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Communication\ReleaseApp;

interface ModuleRatingFetcherInterface
{
    /**
     * @param array<\SprykerSdk\Integrator\Communication\ReleaseApp\ModuleRatingRequestItemDto> $moduleRatingRequestItemDtos
     *
     * @return array<string, \SprykerSdk\Integrator\Communication\ReleaseApp\ModuleRatingResponseItemDto>
     */
    public function fetchModulesRating(array $moduleRatingRequestItemDtos): array;
}
