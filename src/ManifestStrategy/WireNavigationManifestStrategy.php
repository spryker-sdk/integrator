<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\ManifestStrategy;

use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\Exception\UnexpectedNavigationXmlStructureException;

class WireNavigationManifestStrategy extends AbstractNavigationManifestStrategy
{
    /**
     * @return string
     */
    public function getType(): string
    {
        return 'wire-navigation';
    }

    /**
     * @param array<mixed> $manifest
     * @param string $moduleName
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $inputOutput
     * @param bool $isDry
     *
     * @return bool
     */
    public function apply(array $manifest, string $moduleName, InputOutputInterface $inputOutput, bool $isDry): bool
    {
        try {
            $navigation = $this->getNavigation();
        } catch (UnexpectedNavigationXmlStructureException $unexpectedNavigationXmlStructureException) {
            return false;
        }

        $navigation = $this->applyNewNavigation(
            $navigation,
            $manifest[static::KEY_NAVIGATIONS_CONFIGURATION],
            $manifest[static::KEY_NAVIGATION_POSITION_BEFORE] ?? null,
            $manifest[static::KEY_NAVIGATION_POSITION_AFTER] ?? null,
        );

        return $this->writeNavigationSchema($navigation, $inputOutput, $isDry);
    }

    /**
     * @param array<string|int, array<string, mixed>> $navigation
     * @param array<string|int, array<string, mixed>> $newNavigations
     * @param string|null $before
     * @param string|null $after
     *
     * @return array<string|int, array<string, mixed>>
     */
    protected function applyNewNavigation(
        array $navigation,
        array $newNavigations,
        ?string $before = null,
        ?string $after = null
    ): array {
        $newNavigations = $this->prepareNewNavigationsToApplying($newNavigations);
        foreach ($newNavigations as $key => &$value) {
            if (array_key_exists($key,$navigation )) {
                $value = array_replace_recursive($navigation[$key], $value);
            }
        }

        $key = $before ?? $after;
        $position = array_search($key, array_keys($navigation));

        if ($position === false) {
            return array_replace_recursive($navigation, $newNavigations);
        }

        $offset = $position + 1;
        if ($before !== null) {
            $offset--;
        }

        return array_slice($navigation, 0, $offset, true)
            + $newNavigations
            + array_slice($navigation, $offset, null, true);
    }

    /**
     * @param array<string|int, array<string, mixed>> $newNavigations
     *
     * @return array<string|int, array<string, mixed>>
     */
    protected function prepareNewNavigationsToApplying(array $newNavigations): array
    {
        $resultNewNavigations = [];
        $navigationDataReplacingMap = [
            static::NAVIGATION_DATA_KEY_MODULE => static::NAVIGATION_DATA_KEY_BUNDLE,
        ];

        foreach ($newNavigations as $navigationKey => $navigationData) {
            $newNavigationData = [];

            foreach ($navigationData as $navigationDataKey => $navigationDataValue) {
                if (array_key_exists($navigationDataKey, $navigationDataReplacingMap)) {
                    $newNavigationData[$navigationDataReplacingMap[$navigationDataKey]] = $navigationDataValue;

                    continue;
                }

                $newNavigationData[$navigationDataKey] = $navigationDataValue;
            }

            $resultNewNavigations[$navigationKey] = $newNavigationData;

            if (!isset($resultNewNavigations[$navigationKey][static::NAVIGATION_DATA_KEY_PAGES])) {
                continue;
            }

            $resultNewNavigations[$navigationKey][static::NAVIGATION_DATA_KEY_PAGES] = $this->prepareNewNavigationsToApplying(
                $resultNewNavigations[$navigationKey][static::NAVIGATION_DATA_KEY_PAGES],
            );
        }

        return $resultNewNavigations;
    }
}
