<?php

namespace SprykerSdkTest\Integrator\ManifestGenerator;

use SprykerSdk\Integrator\ManifestGenerator\GlossaryKeyManifestStrategy;
use SprykerSdkTest\Integrator\BaseTestCase;

class GlossaryKeyManifestStrategyTest extends BaseTestCase
{
    /**
     * @var string
     */
    protected const CURRENT_FILE_NAME = 'tests/_test_files/manifests/glossary_key/current/glossary.csv';

    /**
     * @var string
     */
    protected const ORIGINAL_FILE_NAME = 'tests/_test_files/manifests/glossary_key/original/glossary.csv';

    /**
     * @return void
     */
    public function testStrategy(): void
    {
        $configStrategy = new GlossaryKeyManifestStrategy();
        $result = [];

        $result = $configStrategy->generateManifestData(static::CURRENT_FILE_NAME, static::ORIGINAL_FILE_NAME, $result);

        $this->assertEquals([
            '?.Cart' => [
                'glossary-key' => [
                    'new' => [
                        'cart.shipping.order-item' => [
                            'de_DE' => '+ Versand',
                            'en_US' => '+ Shipping',
                        ],
                        'cart.shipping' => [
                            'de_DE' => 'Versand',
                            'en_US' => 'Shipping',
                        ],
                    ],
                ],
            ],
        ], $result);
    }
}
