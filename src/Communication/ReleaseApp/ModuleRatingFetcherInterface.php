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
     * @param \SprykerSdk\Integrator\Communication\ReleaseApp\ModulesRatingRequestDto $modulesRatingRequestDto
     *
     * @return \SprykerSdk\Integrator\Communication\ReleaseApp\ModulesRatingResponseDto
     */
    public function fetchModulesRating(ModulesRatingRequestDto $modulesRatingRequestDto): ModulesRatingResponseDto;
}
