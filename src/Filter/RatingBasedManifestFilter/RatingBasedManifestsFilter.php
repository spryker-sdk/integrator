<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Filter\RatingBasedManifestFilter;

use SprykerSdk\Integrator\Communication\ReleaseApp\ModuleRatingFetcherInterface;
use SprykerSdk\Integrator\Communication\ReleaseApp\ModulesRatingResponseDto;
use SprykerSdk\Integrator\Configuration\ConfigurationProviderInterface;
use SprykerSdk\Integrator\Filter\ManifestsFilterInterface;
use SprykerSdk\Integrator\IntegratorConfig;

class RatingBasedManifestsFilter implements ManifestsFilterInterface
{
    /**
     * @var int
     */
    public const MAX_RATING_THRESHOLD = 100;

    /**
     * @var int
     */
    public const MIN_RATING_THRESHOLD = 0;

    /**
     * @var \SprykerSdk\Integrator\Configuration\ConfigurationProviderInterface
     */
    protected ConfigurationProviderInterface $configurationProvider;

    /**
     * @var \SprykerSdk\Integrator\Communication\ReleaseApp\ModuleRatingFetcherInterface
     */
    protected ModuleRatingFetcherInterface $moduleRatingFetcher;

    /**
     * @var \SprykerSdk\Integrator\Filter\RatingBasedManifestFilter\ManifestToModulesRatingRequestMapper
     */
    protected ManifestToModulesRatingRequestMapper $manifestToModulesRatingRequestMapper;

    /**
     * @param \SprykerSdk\Integrator\Configuration\ConfigurationProviderInterface $configurationProvider
     * @param \SprykerSdk\Integrator\Communication\ReleaseApp\ModuleRatingFetcherInterface $moduleRatingFetcher
     * @param \SprykerSdk\Integrator\Filter\RatingBasedManifestFilter\ManifestToModulesRatingRequestMapper $manifestToModulesRatingRequestMapper
     */
    public function __construct(
        ConfigurationProviderInterface $configurationProvider,
        ModuleRatingFetcherInterface $moduleRatingFetcher,
        ManifestToModulesRatingRequestMapper $manifestToModulesRatingRequestMapper
    ) {
        $this->configurationProvider = $configurationProvider;
        $this->moduleRatingFetcher = $moduleRatingFetcher;
        $this->manifestToModulesRatingRequestMapper = $manifestToModulesRatingRequestMapper;
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

        $modulesRatingResponse = $this->moduleRatingFetcher->fetchModulesRating(
            $this->manifestToModulesRatingRequestMapper->mapManifestsToModulesRatingRequest($manifests),
        );

        $filteredManifests = $this->filterManifestsByModulesRating(
            $manifests,
            $this->indexResponseByModuleIdWithRating($modulesRatingResponse),
            $requiredRating,
        );

        return $this->removeEmptyModules($filteredManifests);
    }

    /**
     * @param array<mixed> $manifests
     * @param array<string, int> $modulesRatings
     * @param int $requiredRating
     *
     * @return array<mixed>
     */
    protected function filterManifestsByModulesRating(
        array $manifests,
        array $modulesRatings,
        int $requiredRating
    ): array {
        foreach ($manifests as $fullModuleName => $strategies) {
            foreach ($strategies as $strategyName => $strategy) {
                foreach ($strategy as $index => $manifest) {
                    [$organization, $moduleName] = explode('.', $manifest[IntegratorConfig::MODULE_KEY]);
                    $moduleVersionName = $manifest[IntegratorConfig::MODULE_VERSION_KEY];

                    $moduleId = sprintf('%s:%s:%s', $organization, $moduleName, $moduleVersionName);

                    if (!isset($modulesRatings[$moduleId]) || $modulesRatings[$moduleId] < $requiredRating) {
                        unset($manifests[$fullModuleName][$strategyName][$index]);
                    }
                }
            }
        }

        return $manifests;
    }

    /**
     * @param \SprykerSdk\Integrator\Communication\ReleaseApp\ModulesRatingResponseDto $modulesRatingResponseDto
     *
     * @return array<string, int>
     */
    protected function indexResponseByModuleIdWithRating(ModulesRatingResponseDto $modulesRatingResponseDto): array
    {
        $indexedModuleRating = [];

        foreach ($modulesRatingResponseDto->getModuleRatingResponseDtos() as $moduleRatingResponseDto) {
            $moduleId = sprintf(
                '%s:%s:%s',
                $moduleRatingResponseDto->getOrganization(),
                $moduleRatingResponseDto->getName(),
                $moduleRatingResponseDto->getVersion(),
            );

            $indexedModuleRating[$moduleId] = $moduleRatingResponseDto->getRating();
        }

        return $indexedModuleRating;
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

                    continue;
                }

                $manifests[$fullModuleName][$strategyName] = array_values($manifests[$fullModuleName][$strategyName]);
            }
            if (count($manifests[$fullModuleName]) === 0) {
                unset($manifests[$fullModuleName]);
            }
        }

        return $manifests;
    }
}
