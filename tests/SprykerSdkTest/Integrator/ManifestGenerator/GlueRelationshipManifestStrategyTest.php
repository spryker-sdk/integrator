<?php

namespace SprykerSdkTest\Integrator\ManifestGenerator;

use SprykerSdk\Integrator\ManifestGenerator\GlueRelationshipManifestStrategy;
use SprykerSdkTest\Integrator\BaseTestCase;

class GlueRelationshipManifestStrategyTest extends BaseTestCase
{
    /**
     * @var string
     */
    protected const CURRENT_FILE_NAME = 'tests/_test_files/manifests/dependency_provider/current/GlueApplicationDependencyProvider.php';

    /**
     * @var string
     */
    protected const ORIGINAL_FILE_NAME = 'tests/_test_files/manifests/dependency_provider/original/GlueApplicationDependencyProvider.php';

    /**
     * @return void
     */
    public function testStrategy(): void
    {
        $glueRelationshipStrategy = new GlueRelationshipManifestStrategy();
        $result = [];

        $result = $glueRelationshipStrategy->generateManifestData(static::CURRENT_FILE_NAME, static::ORIGINAL_FILE_NAME, $result);

        $this->assertEquals([
            'Spryker.ProductsRestApi' => [
                'wire-glue-relationship' => [
                    [
                        'source' => [
                            '\Spryker\Glue\QuoteRequestsRestApi\QuoteRequestsRestApiConfig::RESOURCE_QUOTE_REQUESTS' => '\Spryker\Glue\ProductsRestApi\Plugin\GlueApplication\ConcreteProductByQuoteRequestResourceRelationshipPlugin',
                        ],
                    ],
                ],
            ],
            'Spryker.CustomersRestApi' => [
                'unwire-glue-relationship' => [
                    [
                        'source' => [
                            '\Spryker\Glue\QuoteRequestsRestApi\QuoteRequestsRestApiConfig::RESOURCE_QUOTE_REQUESTS' => '\Spryker\Glue\CustomersRestApi\Plugin\GlueApplication\CustomerByQuoteRequestResourceRelationshipPlugin',
                        ],
                    ],
                ],
            ],
        ], $result);
    }

    /**
     * @return void
     */
    public function testStrategyWithNotExistedOriginalFle(): void
    {
        $glueRelationshipStrategy = new GlueRelationshipManifestStrategy();
        $result = [];

        $result = $glueRelationshipStrategy->generateManifestData(static::CURRENT_FILE_NAME, 'not_existed_file.php', $result);

        $this->assertEquals([
            'Spryker.ProductsRestApi' => [
                'wire-glue-relationship' => [
                    [
                        'source' => [
                            '\Spryker\Glue\QuoteRequestsRestApi\QuoteRequestsRestApiConfig::RESOURCE_QUOTE_REQUESTS' => '\Spryker\Glue\ProductsRestApi\Plugin\GlueApplication\ConcreteProductByQuoteRequestResourceRelationshipPlugin',
                        ],
                    ],
                ],
            ],
        ], $result);
    }
}
