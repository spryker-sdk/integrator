<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Communication\ReleaseApp;

use GuzzleHttp\ClientInterface;
use SprykerSdk\Integrator\Configuration\ConfigurationProviderInterface;

class ModuleRatingFetcher implements ModuleRatingFetcherInterface
{
    /**
     * @var int
     */
    protected const DEFAULT_TIMEOUT = 30;

    /**
     * @var string
     */
    protected const MANIFEST_RATING_URL = '/modules-rating.json';

    /**
     * @var \GuzzleHttp\ClientInterface
     */
    protected ClientInterface $client;

    /**
     * @var \SprykerSdk\Integrator\Configuration\ConfigurationProviderInterface
     */
    protected ConfigurationProviderInterface $configurationProvider;

    /**
     * @var \SprykerSdk\Integrator\Communication\ReleaseApp\ModuleRatingResponseMapper
     */
    protected ModuleRatingResponseMapper $moduleRatingResponseMapper;

    /**
     * @param \GuzzleHttp\ClientInterface $client
     * @param \SprykerSdk\Integrator\Configuration\ConfigurationProviderInterface $configurationProvider
     * @param \SprykerSdk\Integrator\Communication\ReleaseApp\ModuleRatingResponseMapper $moduleRatingResponseMapper
     */
    public function __construct(
        ClientInterface $client,
        ConfigurationProviderInterface $configurationProvider,
        ModuleRatingResponseMapper $moduleRatingResponseMapper
    ) {
        $this->client = $client;
        $this->configurationProvider = $configurationProvider;
        $this->moduleRatingResponseMapper = $moduleRatingResponseMapper;
    }

    /**
     * @param \SprykerSdk\Integrator\Communication\ReleaseApp\ModulesRatingRequestDto $modulesRatingRequestDto
     *
     * @return \SprykerSdk\Integrator\Communication\ReleaseApp\ModulesRatingResponseDto
     */
    public function fetchModulesRating(ModulesRatingRequestDto $modulesRatingRequestDto): ModulesRatingResponseDto
    {
        $response = $this->client->request(
            'POST',
            rtrim($this->configurationProvider->getReleaseAppUrl(), '/') . static::MANIFEST_RATING_URL,
            [
                'headers' => ['Content-Type' => 'application/json'],
                'body' => json_encode($modulesRatingRequestDto, \JSON_THROW_ON_ERROR),
                'timeout' => static::DEFAULT_TIMEOUT,
            ],
        );

        return $this->moduleRatingResponseMapper->mapToModulesRatingResponseDto((string)$response->getBody());
    }
}
