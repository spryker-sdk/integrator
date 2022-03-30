<?php

namespace SprykerSdkTest\Integrator\ManifestGenerator\Validator;

use SprykerSdk\Integrator\ManifestGenerator\ArrayConfigElementManifestStrategy;
use SprykerSdk\Integrator\ManifestGenerator\Validator\ArrayConfigElementManifestValidatorStrategy;
use SprykerSdk\Integrator\ManifestGenerator\Validator\ManifestValidatorStrategyInterface;
use SprykerSdkTest\Integrator\BaseTestCase;

class ArrayConfigElementManifestValidatorStrategyTest extends BaseTestCase
{
    /**
     * @return void
     */
    public function testValidatorStrategy(): void
    {
        $validatorStrategy = $this->getValidatorStrategy();
        $validationData = [
            [
                'target' => '\\Pyz\\Client\\RabbitMq\\RabbitMqConfig::getSynchronizationQueueConfiguration',
                'value' => '\\Spryker\\Shared\\AssetExternalStorage\\AssetExternalStorageConfig::ASSET_EXTERNAL_SYNC_STORAGE_QUEUE',
            ],
        ];

        $isApplicable = $validatorStrategy->isApplicable(ArrayConfigElementManifestStrategy::MANIFEST_KEY);
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
                'target' => '\\Pyz\\Client\\RabbitMq\\RabbitMqConfig::getSynchronizationQueueConfiguration',
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
        return new ArrayConfigElementManifestValidatorStrategy();
    }
}
