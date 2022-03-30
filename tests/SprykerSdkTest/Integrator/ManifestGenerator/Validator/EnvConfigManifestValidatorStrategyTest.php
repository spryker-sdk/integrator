<?php

namespace SprykerSdkTest\Integrator\ManifestGenerator\Validator;

use SprykerSdk\Integrator\ManifestGenerator\EnvConfigManifestStrategy;
use SprykerSdk\Integrator\ManifestGenerator\Validator\EnvConfigManifestValidatorStrategy;
use SprykerSdkTest\Integrator\BaseTestCase;

class EnvConfigManifestValidatorStrategyTest extends BaseTestCase
{
    /**
     * @return void
     */
    public function testValidatorStrategy(): void
    {
        $validatorStrategy = new EnvConfigManifestValidatorStrategy();
        $validationData = [
            [
                'target' => '\Spryker\Shared\Kernel\KernelConstants::RESOLVABLE_CLASS_NAMES_CACHE_ENABLED',
                'value' => true,
            ],
        ];

        $isApplicable = $validatorStrategy->isApplicable(EnvConfigManifestStrategy::MANIFEST_KEY);
        $validationResult = $validatorStrategy->validate($validationData);

        $this->assertTrue($isApplicable);
        $this->assertNull($validationResult);
    }

    /**
     * @return void
     */
    public function testValidationStrategyIsNotApplicable(): void
    {
        $validatorStrategy = new EnvConfigManifestValidatorStrategy();

        $isApplicable = $validatorStrategy->isApplicable('wrong-key');

        $this->assertFalse($isApplicable);
    }

    /**
     * @return void
     */
    public function testValidationStrategyReturnsError(): void
    {
        $validatorStrategy = new EnvConfigManifestValidatorStrategy();
        $validationData = [
            [
                'target' => '\Spryker\Shared\Kernel\KernelConstants::RESOLVABLE_CLASS_NAMES_CACHE_ENABLED',
            ],
        ];

        $validationResult = $validatorStrategy->validate($validationData);

        $this->assertSame(
            sprintf('Missing required key `value` in %s record', json_encode($validationData[0])),
            $validationResult,
        );
    }
}
