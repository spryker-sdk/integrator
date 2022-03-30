<?php

namespace SprykerSdk\Integrator\ManifestGenerator\Validator;

interface ManifestValidatorStrategyInterface
{
    /**
     * @param string $manifestKey
     *
     * @return bool
     */
    public function isApplicable(string $manifestKey): bool;

    /**
     * @param array $manifestData
     *
     * @return string|null
     */
    public function validate(array $manifestData): ?string;
}
