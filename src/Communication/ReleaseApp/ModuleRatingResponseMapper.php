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
     * @return array<\SprykerSdk\Integrator\Communication\ReleaseApp\ModuleRatingResponseItemDto>
     */
    public function mapToResponseItems(string $responseBody): array
    {
        $data = json_decode($responseBody, true, 512, \JSON_THROW_ON_ERROR);

        if (!isset($data['result'])) {
            throw new InvalidArgumentException(sprintf('Invalid rating response: %s', $responseBody));
        }

        $modules = $data['result'];

        if (!is_array($data['result'])) {
            throw new InvalidArgumentException(sprintf('Invalid rating response: %s', $responseBody));
        }

        return $this->mapModulesToResponse($modules);
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
        $responseItems = [];

        foreach ($modules as $moduleId => $module) {
            if (!isset($module['name'], $module['version'], $module['rating'], $module['releaseGroupId'], $module['organization'])) {
                throw new InvalidArgumentException(sprintf('Invalid module data: %s', json_encode($module, \JSON_THROW_ON_ERROR)));
            }

            $responseItems[$moduleId] = new ModuleRatingResponseItemDto(
                $module['name'],
                $module['organization'],
                $module['version'],
                $module['rating'],
                $module['releaseGroupId'],
            );
        }

        return $responseItems;
    }
}
