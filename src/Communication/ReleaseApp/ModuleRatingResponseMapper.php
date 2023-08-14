<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Communication\ReleaseApp;

use InvalidArgumentException;

class ModuleRatingResponseMapper
{
    /**
     * @param string $responseBody
     *
     * @throws \InvalidArgumentException
     *
     * @return \SprykerSdk\Integrator\Communication\ReleaseApp\ModulesRatingResponseDto
     */
    public function mapToModulesRatingResponseDto(string $responseBody): ModulesRatingResponseDto
    {
        $data = json_decode($responseBody, true, 512, \JSON_THROW_ON_ERROR);

        if (!isset($data['result'])) {
            throw new InvalidArgumentException(sprintf('Invalid rating response: %s', $responseBody));
        }

        $modules = $data['result'];

        if (!is_array($data['result'])) {
            throw new InvalidArgumentException(sprintf('Invalid rating response: %s', $responseBody));
        }

        return new ModulesRatingResponseDto($this->mapModulesToResponse($modules));
    }

    /**
     * @param array $modules
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    protected function mapModulesToResponse(array $modules): array
    {
        $moduleRatingResponseDtos = [];

        foreach ($modules as $module) {
            if (!isset($module['name'], $module['version'], $module['rating'], $module['releaseGroupId'], $module['organization'])) {
                throw new InvalidArgumentException(sprintf('Invalid module data: %s', json_encode($module, \JSON_THROW_ON_ERROR)));
            }

            $moduleRatingResponseDtos[] = new ModuleRatingResponseDto(
                $module['name'],
                $module['organization'],
                $module['version'],
                $module['rating'],
                $module['releaseGroupId'],
            );
        }

        return $moduleRatingResponseDtos;
    }
}
