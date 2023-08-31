<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator\Filter\RatingBasedManifestFilter;

use PHPUnit\Framework\TestCase;
use SprykerSdk\Integrator\Filter\RatingBasedManifestFilter\ManifestToModulesRatingRequestMapper;

class ManifestToModulesRatingRequestMapperTest extends TestCase
{
    /**
     * @return void
     */
    public function testMapManifestsToModulesRatingRequestShouldMapToRequest(): void
    {
        //Arrange
        $manifestJson = <<<'JSON'
        {
        	"Spryker.Customer": {
        		"configure-module": [{
        			"previousValue": "",
        			"target": "\\Spryker\\Zed\\Customer\\CustomerConfig::getCustomerSequenceNumberPrefix",
        			"value": "customer",
        			"module": "Spryker.Customer",
        			"module-version": "7.51.0"
        		}]
        	}
        }
        JSON;

        $manifests = json_decode($manifestJson, true, 512, \JSON_THROW_ON_ERROR);

        $mapper = new ManifestToModulesRatingRequestMapper();

        //Act
        $request = $mapper->mapManifestsToModulesRatingRequest($manifests);

        //Assert
        $this->assertCount(1, $request->getModuleRatingRequestDtos());
        $moduleRatingRequestDto = $request->getModuleRatingRequestDtos()[0];

        $this->assertSame('Spryker', $moduleRatingRequestDto->getOrganizationName());
        $this->assertSame('Customer', $moduleRatingRequestDto->getModuleName());
        $this->assertSame('7.51.0', $moduleRatingRequestDto->getModuleVersion());
    }
}
