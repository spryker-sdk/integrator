<?php

namespace SprykerSdkTest\Integrator\ManifestGenerator\Validator;

use SprykerSdk\Integrator\ManifestGenerator\GlueRelationshipManifestStrategy;
use SprykerSdk\Integrator\ManifestGenerator\Validator\GlueRelationshipManifestValidatorStrategy;
use SprykerSdk\Integrator\ManifestGenerator\Validator\ManifestValidatorStrategyInterface;
use SprykerSdkTest\Integrator\BaseTestCase;

class GlueRelationshipManifestValidatorStrategyTest extends BaseTestCase
{
    /**
     * @return void
     */
    public function testValidatorStrategy(): void
    {
        $validatorStrategy = $this->getValidatorStrategy();
        $validationData = [
            [
                'source' => [
                    '\Spryker\Glue\QuoteRequestsRestApi\QuoteRequestsRestApiConfig::RESOURCE_QUOTE_REQUESTS' => '\Spryker\Glue\ProductsRestApi\Plugin\GlueApplication\ConcreteProductByQuoteRequestResourceRelationshipPlugin',
                ],
            ],
        ];

        $isApplicable = $validatorStrategy->isApplicable(GlueRelationshipManifestStrategy::MANIFEST_KEY_WIRE);
        $validationResult = $validatorStrategy->validate($validationData);

        $this->assertTrue($isApplicable);
        $this->assertNull($validationResult);
    }

    /**
     * @return void
     */
    public function testValidationStrategyIsNotApplicable(): void
    {
        $validatorStrategy = $this->getValidatorStrategy();

        $isApplicable = $validatorStrategy->isApplicable('wrong-key');

        $this->assertFalse($isApplicable);
    }

    /**
     * @return void
     */
    public function testValidationStrategyReturnsError(): void
    {
        $validatorStrategy = $this->getValidatorStrategy();
        $validationData = [
            [
                [
                    '\Spryker\Glue\QuoteRequestsRestApi\QuoteRequestsRestApiConfig::RESOURCE_QUOTE_REQUESTS' => '\Spryker\Glue\ProductsRestApi\Plugin\GlueApplication\ConcreteProductByQuoteRequestResourceRelationshipPlugin',
                ],
            ],
        ];

        $validationResult = $validatorStrategy->validate($validationData);

        $this->assertSame(
            sprintf('Missing required key `source` in %s record', json_encode($validationData[0])),
            $validationResult,
        );
    }

    /**
     * @return \App\Manifest\Validator\ManifestValidatorStrategyInterface
     */
    protected function getValidatorStrategy(): ManifestValidatorStrategyInterface
    {
        return new GlueRelationshipManifestValidatorStrategy();
    }
}
