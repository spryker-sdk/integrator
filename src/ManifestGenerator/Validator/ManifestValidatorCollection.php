<?php

namespace SprykerSdk\Integrator\ManifestGenerator\Validator;

class ManifestValidatorCollection
{
    /**
     * @var array<string>
     */
    protected const VALIDATORS = [
        EnvConfigManifestValidatorStrategy::class,
        GlueRelationshipManifestValidatorStrategy::class,
        ModuleConfigManifestValidatorStrategy::class,
        PluginsManifestValidatorStrategy::class,
        WidgetManifestValidatorStrategy::class,
        ArrayConfigElementManifestValidatorStrategy::class,
        GlossaryKeyManifestValidatorStrategy::class,
    ];

    /**
     * @var array<\SprykerSdk\Integrator\ManifestGenerator\Validator\ManifestValidatorStrategyInterface>
     */
    protected static $validatorsCache = [];

    /**
     * @param string $manifestKey
     * @param array $manifestData
     *
     * @return string|null
     */
    public function validate(string $manifestKey, array $manifestData): ?string
    {
        $validators = $this->getValidators();

        foreach ($validators as $validator) {
            if (!$validator->isApplicable($manifestKey)) {
                continue;
            }

            return $validator->validate($manifestData);
        }

        return sprintf('Manifest key `%s` is not supported', $manifestKey);
    }

    /**
     * @return array<\SprykerSdk\Integrator\ManifestGenerator\Validator\ManifestValidatorStrategyInterface>
     */
    protected function getValidators(): array
    {
        if (static::$validatorsCache) {
            return static::$validatorsCache;
        }

        foreach (static::VALIDATORS as $validatorClassName) {
            static::$validatorsCache[] = new $validatorClassName();
        }

        return static::$validatorsCache;
    }
}
