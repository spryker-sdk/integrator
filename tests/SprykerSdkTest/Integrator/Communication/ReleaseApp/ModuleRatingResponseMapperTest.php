<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator\Communication\ReleaseApp;

use Exception;
use PHPUnit\Framework\TestCase;
use SprykerSdk\Integrator\Communication\ReleaseApp\ModuleRatingResponseMapper;

class ModuleRatingResponseMapperTest extends TestCase
{
    /**
     * @dataProvider invalidResponseDataProvider
     *
     * @param string $responseBody
     *
     * @return void
     */
    public function testMapToModulesRatingResponseDtoShouldReturnExceptionWhenInvalidResponse(string $responseBody): void
    {
        //Arrange
        $moduleRatingResponseMapper = new ModuleRatingResponseMapper();
        $this->expectException(Exception::class);

        //Act
        $moduleRatingResponseMapper->mapToModulesRatingResponseDto($responseBody);
    }

    /**
     * @return array<string, array<string>>
     */
    public function invalidResponseDataProvider(): array
    {
        return [
            'invalid_json' => ['{invalid json'],
            'no_result_key' => ['{"someVal": 1}'],
            'result_not_array' => ['{"result" => 1}'],
            'no_name_key_set' => ['{"result" : {"module1": {"organization": "Spryker", "version": "1.9.0", "rating": 50, "releaseGroupId": 123}}}'],
            'no_organization_key_set' => ['{"result" : {"module1": {"name": "Acl", "version": "1.9.0", "rating": 50, "releaseGroupId": 123}}}'],
            'no_version_key_set' => ['{"result" : {"module1": {"name": "Acl", "organization": "Spryker", "rating": 50, "releaseGroupId": 123}}}'],
            'no_rating_key_set' => ['{"result" : {"module1": {"name": "Acl", "organization": "Spryker", "version": "1.9.0", "releaseGroupId": 123}}}'],
            'no_releaseGroupId_key_set' => ['{"result" : {"module1": {"name": "Acl", "organization": "Spryker", "version": "1.9.0", "rating": 50}}}'],
        ];
    }

    /**
     * @return void
     */
    public function testMapToModulesRatingResponseDtoShouldReturnValidResponse(): void
    {
        //Arrange
        $responseBody = '{"result" : {"Spryker:Acl:1.9.0": {"name": "Acl", "organization": "Spryker", "version": "1.9.0", "rating": 50, "releaseGroupId": 123}}}';
        $moduleRatingResponseMapper = new ModuleRatingResponseMapper();

        //Act
        $response = $moduleRatingResponseMapper->mapToModulesRatingResponseDto($responseBody);

        //Assert
        $this->assertCount(1, $response->getModuleRatingResponseDtos());
        $moduleRatingResponseDto = $response->getModuleRatingResponseDtos()[0];

        $this->assertSame('Acl', $moduleRatingResponseDto->getName());
        $this->assertSame('Spryker', $moduleRatingResponseDto->getOrganization());
        $this->assertSame(50, $moduleRatingResponseDto->getRating());
        $this->assertSame('1.9.0', $moduleRatingResponseDto->getVersion());
        $this->assertSame(123, $moduleRatingResponseDto->getReleaseGroupId());
    }
}
