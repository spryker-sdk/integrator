<?php

namespace SprykerSdk\Integrator\ManifestGenerator\Validator;

use SprykerSdk\Integrator\ManifestGenerator\ArrayConfigElementManifestStrategy;

class ArrayConfigElementManifestValidatorStrategy implements ManifestValidatorStrategyInterface
{
    /**
     * @param string $manifestKey
     *
     * @return bool
     */
    public function isApplicable(string $manifestKey): bool
    {
        return $manifestKey === ArrayConfigElementManifestStrategy::MANIFEST_KEY;
    }

    /**
     * @param array $manifestData
     *
     * @return string|null
     */
    public function validate(array $manifestData): ?string
    {
        foreach ($manifestData as $manifestRecord) {
            if (!array_key_exists('target', $manifestRecord)) {
                return sprintf('Missing required key `target` in %s record', json_encode($manifestRecord));
            }

            if (!array_key_exists('value', $manifestRecord)) {
                return sprintf('Missing required key `value` in %s record', json_encode($manifestRecord));
            }
        }

        return null;
    }
}
