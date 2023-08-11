<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Filter;

use SprykerSdk\Integrator\Communication\ReleaseApp\ModuleRatingFetcherInterface;
use SprykerSdk\Integrator\Communication\ReleaseApp\ModuleRatingRequestItemDto;
use SprykerSdk\Integrator\Configuration\ConfigurationProviderInterface;
use SprykerSdk\Integrator\IntegratorConfig;

class RatingBasedManifestsFilter implements ManifestsFilterInterface
{
    /**
     * @var int
     */
    protected const MAX_RATING_THRESHOLD = 100;

    /**
     * @var int
     */
    protected const MIN_RATING_THRESHOLD = 0;

    /**
     * @var \SprykerSdk\Integrator\Configuration\ConfigurationProviderInterface
     */
    protected ConfigurationProviderInterface $configurationProvider;

    /**
     * @var \SprykerSdk\Integrator\Communication\ReleaseApp\ModuleRatingFetcherInterface
     */
    protected ModuleRatingFetcherInterface $moduleRatingFetcher;

    /**
     * @param \SprykerSdk\Integrator\Configuration\ConfigurationProviderInterface $configurationProvider
     * @param \SprykerSdk\Integrator\Communication\ReleaseApp\ModuleRatingFetcherInterface $moduleRatingFetcher
     */
    public function __construct(ConfigurationProviderInterface $configurationProvider, ModuleRatingFetcherInterface $moduleRatingFetcher)
    {
        $this->configurationProvider = $configurationProvider;
        $this->moduleRatingFetcher = $moduleRatingFetcher;
    }

    /**
     * @param array<mixed> $manifests
     *
     * @return array<mixed>
     */
    public function filterManifests(array $manifests): array
    {
        $requiredRating = $this->configurationProvider->getManifestsRatingThreshold();

        if ($requiredRating >= static::MAX_RATING_THRESHOLD) {
            return $manifests;
        }

        if ($requiredRating <= static::MIN_RATING_THRESHOLD) {
            return [];
        }

        $modulesRatings = $this->moduleRatingFetcher->fetchModulesRating($this->createModuleRatingFetcherRequest($manifests));

        $filterdManifests = $this->filterManifestsByModulesRating($manifests, $modulesRatings, $requiredRating);

        return $this->removeEmptyModules($filterdManifests);
    }

    /**
     * @param array<mixed> $manifests
     *
     * @return array<\SprykerSdk\Integrator\Communication\ReleaseApp\ModuleRatingRequestItemDto>
     */
    protected function createModuleRatingFetcherRequest(array $manifests): array
    {
        $requestItems = [];

        foreach ($manifests as $strategies) {
            foreach ($strategies as $strategy) {
                foreach ($strategy as $manifest) {
                    [$organization, $moduleName] = explode('.', $manifest[IntegratorConfig::MODULE_KEY]);
                    $moduleVersionName = $manifest[IntegratorConfig::MODULE_VERSION_KEY];
                    $requestItems[] = new ModuleRatingRequestItemDto($organization, $moduleName, $moduleVersionName);
                }
            }
        }

        return $requestItems;
    }

    /**
     * @param array<mixed> $manifests
     * @param array<string, \SprykerSdk\Integrator\Communication\ReleaseApp\ModuleRatingResponseItemDto> $modulesRatings
     * @param int $requiredRating
     *
     * @return array<mixed>
     */
    protected function filterManifestsByModulesRating(array $manifests, array $modulesRatings, int $requiredRating): array
    {
        foreach ($manifests as $fullModuleName => $strategies) {
            foreach ($strategies as $strategyName => $strategy) {
                foreach ($strategy as $index => $manifest) {
                    [$organization, $moduleName] = explode('.', $manifest[IntegratorConfig::MODULE_KEY]);
                    $moduleVersionName = $manifest[IntegratorConfig::MODULE_VERSION_KEY];

                    $moduleId = sprintf('%s:%s:%s', $organization, $moduleName, $moduleVersionName);

                    if (!isset($modulesRatings[$moduleId]) || $modulesRatings[$moduleId]->getRating() < $requiredRating) {
                        unset($manifests[$fullModuleName][$strategyName][$index]);
                    }
                }
            }
        }

        return $manifests;
    }

    /**
     * @param array<mixed> $manifests
     *
     * @return array<mixed>
     */
    protected function removeEmptyModules(array $manifests): array
    {
        foreach ($manifests as $fullModuleName => $strategies) {
            foreach ($strategies as $strategyName => $strategy) {
                if (count($manifests[$fullModuleName][$strategyName]) === 0) {
                    unset($manifests[$fullModuleName][$strategyName]);
                }
            }
            if (count($manifests[$fullModuleName]) === 0) {
                unset($manifests[$fullModuleName]);
            }
        }

        return $manifests;
    }
}
