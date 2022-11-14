<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\ManifestStrategy;

use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\Exception\UnexpectedNavigationXmlStructureException;

class UnwireNavigationManifestStrategy extends AbstractNavigationManifestStrategy
{
    /**
     * @return string
     */
    public function getType(): string
    {
        return 'unwire-navigation';
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
        );

        return $this->writeNavigationSchema($navigation, $inputOutput, $isDry);
    }

    /**
     * @param array<string|int, array<int|string, mixed>> $navigation
     * @param array<string|int, array<string, mixed|null>|null> $manifestData
     *
     * @return array<string|int, array<int|string, mixed>>
     */
    protected function applyNewNavigation(
        array $navigation,
        array $manifestData
    ): array {
        $outputDiff = [];

        foreach ($navigation as $key => $value) {
            if (!array_key_exists($key, $manifestData)) {
                if (!in_array($value, $manifestData)) {
                    $outputDiff[$key] = $value;
                }

                continue;
            }

            if ($manifestData[$key] === null) {
                // Deleted element is found, do not add element to output array
                continue;
            }

            if (is_array($value) && is_array($manifestData[$key])) {
                $recursiveDiff = $this->applyNewNavigation($value, $manifestData[$key]);

                if (count($recursiveDiff)) {
                    $outputDiff[$key] = $recursiveDiff;
                }
            }
        }

        return $outputDiff;
    }
}
