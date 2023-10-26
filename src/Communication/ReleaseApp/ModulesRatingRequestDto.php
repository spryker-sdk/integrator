<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Communication\ReleaseApp;

use JsonSerializable;

/**
 * @codeCoverageIgnore
 */
class ModulesRatingRequestDto implements JsonSerializable
{
    /**
     * @var array<\SprykerSdk\Integrator\Communication\ReleaseApp\ModuleRatingRequestDto>
     */
    protected array $moduleRatingRequestDtos;

    /**
     * @param array<\SprykerSdk\Integrator\Communication\ReleaseApp\ModuleRatingRequestDto> $moduleRatingRequestDtos
     */
    public function __construct(array $moduleRatingRequestDtos = [])
    {
        $this->moduleRatingRequestDtos = $moduleRatingRequestDtos;
    }

    /**
     * @param \SprykerSdk\Integrator\Communication\ReleaseApp\ModuleRatingRequestDto $moduleRatingRequestDto
     *
     * @return void
     */
    public function addModuleRatingRequestDto(ModuleRatingRequestDto $moduleRatingRequestDto): void
    {
        $this->moduleRatingRequestDtos[] = $moduleRatingRequestDto;
    }

    /**
     * @return array<\SprykerSdk\Integrator\Communication\ReleaseApp\ModuleRatingRequestDto>
     */
    public function getModuleRatingRequestDtos(): array
    {
        return $this->moduleRatingRequestDtos;
    }

    /**
     * @return array<\SprykerSdk\Integrator\Communication\ReleaseApp\ModuleRatingRequestDto>
     */
    public function jsonSerialize(): array
    {
        return $this->moduleRatingRequestDtos;
    }
}
