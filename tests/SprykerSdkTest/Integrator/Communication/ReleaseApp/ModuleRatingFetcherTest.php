<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator\Communication\ReleaseApp;

use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use SprykerSdk\Integrator\Communication\ReleaseApp\ModuleRatingFetcher;
use SprykerSdk\Integrator\Communication\ReleaseApp\ModuleRatingRequestDto;
use SprykerSdk\Integrator\Communication\ReleaseApp\ModuleRatingResponseDto;
use SprykerSdk\Integrator\Communication\ReleaseApp\ModuleRatingResponseMapper;
use SprykerSdk\Integrator\Communication\ReleaseApp\ModulesRatingRequestDto;
use SprykerSdk\Integrator\Communication\ReleaseApp\ModulesRatingResponseDto;
use SprykerSdk\Integrator\Configuration\ConfigurationProviderInterface;

class ModuleRatingFetcherTest extends TestCase
{
    /**
     * @return void
     */
    public function testFetchModulesRatingShouldReturnResponse(): void
    {
        //Arrange
        $responseBody = <<<JSON
        {
            "Spryker:ShipmentTypesBackendApi:0.1.0": {
                "name": "ShipmentTypesBackendApi",
                "organization": "Spryker",
                "rating": 50,
                "version": "1.9.0",
                "releaseGroupId": 118
            }
        }
        JSON;

        $modulesRatingRequestDto = new ModulesRatingRequestDto([
            new ModuleRatingRequestDto('Spryker', 'ShipmentTypesBackendApi', '1.9.0'),
        ]);

        $modulesRatingResponseDto = new ModulesRatingResponseDto([
            new ModuleRatingResponseDto('ShipmentTypesBackendApi', 'Spryker', '1.9.0', 50, 118),
        ]);

        $clientMock = $this->createClientMock($responseBody);
        $configurationProviderMock = $this->createConfigurationProviderMock();
        $ModuleRatingResponseMapperMock = $this->createModuleRatingResponseMapperMock($modulesRatingResponseDto, $responseBody);
        $moduleRatingFetcher = new ModuleRatingFetcher($clientMock, $configurationProviderMock, $ModuleRatingResponseMapperMock);

        //Act
        $fetchedResponse = $moduleRatingFetcher->fetchModulesRating($modulesRatingRequestDto);

        //Assert
        $this->assertSame($modulesRatingResponseDto, $fetchedResponse);
    }

    /**
     * @param string $responseBody
     *
     * @return \GuzzleHttp\ClientInterface
     */
    protected function createClientMock(string $responseBody): ClientInterface
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn($responseBody);

        $client = $this->createMock(ClientInterface::class);
        $client->method('request')->willReturn($response);

        return $client;
    }

    /**
     * @return \SprykerSdk\Integrator\Configuration\ConfigurationProviderInterface
     */
    protected function createConfigurationProviderMock(): ConfigurationProviderInterface
    {
        $configurationProvider = $this->createMock(ConfigurationProviderInterface::class);
        $configurationProvider->method('getReleaseAppUrl')->willReturn('');

        return $configurationProvider;
    }

    /**
     * @param \SprykerSdk\Integrator\Communication\ReleaseApp\ModulesRatingResponseDto $response
     * @param string $expectedBody
     *
     * @return \SprykerSdk\Integrator\Communication\ReleaseApp\ModuleRatingResponseMapper
     */
    protected function createModuleRatingResponseMapperMock(ModulesRatingResponseDto $response, string $expectedBody): ModuleRatingResponseMapper
    {
        $moduleRatingResponseMapper = $this->createMock(ModuleRatingResponseMapper::class);
        $moduleRatingResponseMapper
            ->method('mapToModulesRatingResponseDto')
            ->with($this->equalTo($expectedBody))
            ->willReturn($response);

        return $moduleRatingResponseMapper;
    }
}
