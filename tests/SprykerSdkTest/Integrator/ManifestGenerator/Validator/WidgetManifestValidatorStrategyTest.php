<?php

namespace SprykerSdkTest\Integrator\ManifestGenerator\Validator;

use SprykerSdk\Integrator\ManifestGenerator\Validator\ManifestValidatorStrategyInterface;
use SprykerSdk\Integrator\ManifestGenerator\Validator\WidgetManifestValidatorStrategy;
use SprykerSdk\Integrator\ManifestGenerator\WidgetsManifestStrategy;
use SprykerSdkTest\Integrator\BaseTestCase;

class WidgetManifestValidatorStrategyTest extends BaseTestCase
{
    /**
     * @return void
     */
    public function testValidatorStrategy(): void
    {
        $validatorStrategy = $this->getValidatorStrategy();
        $validationData = [
            [
                'source' => '\SprykerShop\Yves\MultiCartWidget\Widget\AddToMultiCartWidget',
            ],
        ];

        $isApplicable = $validatorStrategy->isApplicable(WidgetsManifestStrategy::MANIFEST_KEY_WIRE);
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
                '\SprykerShop\Yves\MultiCartWidget\Widget\AddToMultiCartWidget',
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
        return new WidgetManifestValidatorStrategy();
    }
}
