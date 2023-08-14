<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator\Filter\RatingBasedManifestFilter;

use PHPUnit\Framework\TestCase;
use SprykerSdk\Integrator\Communication\ReleaseApp\ModuleRatingFetcherInterface;
use SprykerSdk\Integrator\Communication\ReleaseApp\ModuleRatingResponseDto;
use SprykerSdk\Integrator\Communication\ReleaseApp\ModulesRatingResponseDto;
use SprykerSdk\Integrator\Configuration\ConfigurationProviderInterface;
use SprykerSdk\Integrator\Filter\RatingBasedManifestFilter\ManifestToModulesRatingRequestMapper;
use SprykerSdk\Integrator\Filter\RatingBasedManifestFilter\RatingBasedManifestsFilter;

class RatingBasedManifestsFilterTest extends TestCase
{
    /**
     * @return void
     */
    public function testsFilterManifestsShouldReturnTheSameManifestsWhenRatingIsMax(): void
    {
        //Arrange
        $manifests = $this->getManifests();
        $ratingBasedManifestsFilter = new RatingBasedManifestsFilter(
            $this->createConfigurationProviderMock(RatingBasedManifestsFilter::MAX_RATING_THRESHOLD),
            $this->createModuleRatingFetcherMock(new ModulesRatingResponseDto([]), false),
            new ManifestToModulesRatingRequestMapper(),
        );

        //Act
        $filteredManifests = $ratingBasedManifestsFilter->filterManifests($manifests);

        //Assert
        $this->assertSame($manifests, $filteredManifests);
    }

    /**
     * @return void
     */
    public function testsFilterManifestsShouldReturnEmptyManifestsWhenRatingIsMin(): void
    {
        //Arrange
        $manifests = $this->getManifests();
        $ratingBasedManifestsFilter = new RatingBasedManifestsFilter(
            $this->createConfigurationProviderMock(RatingBasedManifestsFilter::MIN_RATING_THRESHOLD),
            $this->createModuleRatingFetcherMock(new ModulesRatingResponseDto([]), false),
            new ManifestToModulesRatingRequestMapper(),
        );

        //Act
        $filteredManifests = $ratingBasedManifestsFilter->filterManifests($manifests);

        //Assert
        $this->assertSame([], $filteredManifests);
    }

    /**
     * @return void
     */
    public function testsFilterManifestsShouldFilterByManifestsRating(): void
    {
        //Arrange
        $manifests = $this->getManifests();
        $ratingThreshold = 50;
        $ratingBasedManifestsFilter = new RatingBasedManifestsFilter(
            $this->createConfigurationProviderMock($ratingThreshold),
            $this->createModuleRatingFetcherMock(new ModulesRatingResponseDto([
                new ModuleRatingResponseDto('ErrorHandler', 'Spryker', '2.7.0', 70, 131),
                new ModuleRatingResponseDto('ErrorHandler', 'Spryker', '2.8.0', 49, 132),
                new ModuleRatingResponseDto('Customer', 'Spryker', '7.51.0', 1, 133),
                new ModuleRatingResponseDto('PropelOrm', 'Spryker', '1.19.0', 30, 134),
            ])),
            new ManifestToModulesRatingRequestMapper(),
        );

        //Act
        $filteredManifests = $ratingBasedManifestsFilter->filterManifests($manifests);

        //Assert
        $this->assertSame([
        'Spryker.ErrorHandler' => [
        'configure-module' => [[
            'previousValue' => '',
            'target' => '\\Spryker\\Zed\\ErrorHandler\\ErrorHandlerConfig::getValidSubRequestExceptionStatusCodes',
            'value' => [
                'value' => 'return array_merge(parent::getValidSubRequestExceptionStatusCodes(), [\\Symfony\\Component\\HttpFoundation\\Response::HTTP_TOO_MANY_REQUESTS])',
                'is_literal' => true,
            ],
            'module' => 'Spryker.ErrorHandler',
            'module-version' => '2.7.0',
        ]]]], $filteredManifests);
    }

    /**
     * @param int $ratingThreshold
     *
     * @return \SprykerSdk\Integrator\Configuration\ConfigurationProviderInterface
     */
    protected function createConfigurationProviderMock(int $ratingThreshold): ConfigurationProviderInterface
    {
        $configurationProvider = $this->createMock(ConfigurationProviderInterface::class);
        $configurationProvider->method('getManifestsRatingThreshold')->willReturn($ratingThreshold);

        return $configurationProvider;
    }

    /**
     * @param \SprykerSdk\Integrator\Communication\ReleaseApp\ModulesRatingResponseDto $modulesRatingResponseDto
     * @param bool $shouldBeInvoked
     *
     * @return \SprykerSdk\Integrator\Communication\ReleaseApp\ModuleRatingFetcherInterface
     */
    protected function createModuleRatingFetcherMock(
        ModulesRatingResponseDto $modulesRatingResponseDto,
        bool $shouldBeInvoked = true
    ): ModuleRatingFetcherInterface {
        $moduleRatingFetcher = $this->createMock(ModuleRatingFetcherInterface::class);

        $shouldBeInvoked
            ? $moduleRatingFetcher->method('fetchModulesRating')->willReturn($modulesRatingResponseDto)
            : $moduleRatingFetcher->expects($this->never())->method('fetchModulesRating');

        return $moduleRatingFetcher;
    }

    /**
     * @return array<mixed>
     */
    protected function getManifests(): array
    {
        $manifest = <<<'JSON'
        {
            "Spryker.Customer": {
                "configure-module": [
                    {
                        "previousValue": "",
                        "target": "\\Spryker\\Zed\\Customer\\CustomerConfig::getCustomerSequenceNumberPrefix",
                        "value": "customer",
                        "module": "Spryker.Customer",
                        "module-version": "7.51.0"
                    }
                ]
            },
            "Spryker.ErrorHandler": {
                "configure-module": [
                    {
                        "previousValue": "",
                        "target": "\\Spryker\\Zed\\ErrorHandler\\ErrorHandlerConfig::getValidSubRequestExceptionStatusCodes",
                        "value": {
                            "value": "return array_merge(parent::getValidSubRequestExceptionStatusCodes(), [\\Symfony\\Component\\HttpFoundation\\Response::HTTP_TOO_MANY_REQUESTS])",
                            "is_literal": true
                        },
                        "module": "Spryker.ErrorHandler",
                        "module-version": "2.8.0"
                    },
                    {
                        "previousValue": "",
                        "target": "\\Spryker\\Zed\\ErrorHandler\\ErrorHandlerConfig::getValidSubRequestExceptionStatusCodes",
                        "value": {
                            "value": "return array_merge(parent::getValidSubRequestExceptionStatusCodes(), [\\Symfony\\Component\\HttpFoundation\\Response::HTTP_TOO_MANY_REQUESTS])",
                            "is_literal": true
                        },
                        "module": "Spryker.ErrorHandler",
                        "module-version": "2.7.0"
                    }
                ]
            },
            "Spryker.PropelOrm": {
                "configure-module": [
                    {
                        "value": true,
                        "target": "\\Spryker\\Zed\\PropelOrm\\PropelOrmConfig::isBooleanCastingEnabled",
                        "module": "Spryker.PropelOrm",
                        "module-version": "1.19.0"
                    }
                ]
            }
        }
        JSON;

        return json_decode($manifest, true, 512, \JSON_THROW_ON_ERROR);
    }
}
