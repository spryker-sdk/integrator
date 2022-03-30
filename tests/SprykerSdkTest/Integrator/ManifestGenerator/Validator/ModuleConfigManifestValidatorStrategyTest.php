<?php

namespace SprykerSdkTest\Integrator\ManifestGenerator\Validator;

use SprykerSdk\Integrator\ManifestGenerator\ModuleConfigManifestStrategy;
use SprykerSdk\Integrator\ManifestGenerator\Validator\ManifestValidatorStrategyInterface;
use SprykerSdk\Integrator\ManifestGenerator\Validator\ModuleConfigManifestValidatorStrategy;
use SprykerSdkTest\Integrator\BaseTestCase;

class ModuleConfigManifestValidatorStrategyTest extends BaseTestCase
{
    /**
     * @return void
     */
    public function testValidatorStrategy(): void
    {
        $validatorStrategy = $this->getValidatorStrategy();
        $validationData = [
            [
                'target' => '\SprykerShop\Yves\CartPage\CartPageConfig::getConfigMethod',
                'value' => '\SprykerShop\Yves\CartPage\CartPageConfig::SOME_VALUE',
            ],
        ];

        $isApplicable = $validatorStrategy->isApplicable(ModuleConfigManifestStrategy::MANIFEST_KEY);
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
                'target' => '\SprykerShop\Yves\CartPage\CartPageConfig::getConfigMethod',
            ],
        ];

        $validationResult = $validatorStrategy->validate($validationData);

        $this->assertSame(
            sprintf('Missing required key `value` in %s record', json_encode($validationData[0])),
            $validationResult,
        );
    }

    /**
     * @return \App\Manifest\Validator\ManifestValidatorStrategyInterface
     */
    protected function getValidatorStrategy(): ManifestValidatorStrategyInterface
    {
        return new ModuleConfigManifestValidatorStrategy();
    }
}
