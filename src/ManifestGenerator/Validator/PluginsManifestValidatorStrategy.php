<?php

namespace SprykerSdk\Integrator\ManifestGenerator\Validator;

use SprykerSdk\Integrator\ManifestGenerator\PluginsManifestStrategy;

class PluginsManifestValidatorStrategy implements ManifestValidatorStrategyInterface
{
    /**
     * @param string $manifestKey
     *
     * @return bool
     */
    public function isApplicable(string $manifestKey): bool
    {
        return in_array($manifestKey, [
            PluginsManifestStrategy::MANIFEST_KEY_WIRE,
            PluginsManifestStrategy::MANIFEST_KEY_UNWIRE,
        ], true);
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

            if (!array_key_exists('source', $manifestRecord)) {
                return sprintf('Missing required key `source` in %s record', json_encode($manifestRecord));
            }
        }

        return null;
    }
}
