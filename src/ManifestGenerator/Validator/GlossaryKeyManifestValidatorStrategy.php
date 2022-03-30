<?php

namespace SprykerSdk\Integrator\ManifestGenerator\Validator;

use SprykerSdk\Integrator\ManifestGenerator\GlossaryKeyManifestStrategy;

class GlossaryKeyManifestValidatorStrategy implements ManifestValidatorStrategyInterface
{
    /**
     * @param string $manifestKey
     *
     * @return bool
     */
    public function isApplicable(string $manifestKey): bool
    {
        return $manifestKey === GlossaryKeyManifestStrategy::MANIFEST_KEY;
    }

    /**
     * @param array $manifestData
     *
     * @return string|null
     */
    public function validate(array $manifestData): ?string
    {
        if (!array_key_exists('new', $manifestData)) {
            return sprintf('Missing required key `new` in %s record', json_encode($manifestData));
        }

        return null;
    }
}
