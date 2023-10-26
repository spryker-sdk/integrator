<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Communication\ReleaseApp;

/**
 * @codeCoverageIgnore
 */
class ModulesRatingResponseDto
{
    /**
     * @var array<\SprykerSdk\Integrator\Communication\ReleaseApp\ModuleRatingResponseDto>
     */
    protected array $moduleRatingResponseDtos;

    /**
     * @param array<\SprykerSdk\Integrator\Communication\ReleaseApp\ModuleRatingResponseDto> $moduleRatingResponseDtos
     */
    public function __construct(array $moduleRatingResponseDtos)
    {
        $this->moduleRatingResponseDtos = $moduleRatingResponseDtos;
    }

    /**
     * @return array<\SprykerSdk\Integrator\Communication\ReleaseApp\ModuleRatingResponseDto>
     */
    public function getModuleRatingResponseDtos(): array
    {
        return $this->moduleRatingResponseDtos;
    }
}
