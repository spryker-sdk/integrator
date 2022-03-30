<?php

namespace SprykerSdk\Integrator\ManifestGenerator\ArrayElementExtractor\ArrayElementNamespaceExtractor;

class ConstArrayElementNamespaceExtractor implements ArrayElementNamespaceExtractorInterface
{
    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function isApplicable($value): bool
    {
        return is_string($value) && strpos($value, '::') !== false;
    }

    /**
     * @param string $value
     *
     * @return string|null
     */
    public function extractNamespace($value): ?string
    {
        $constantParts = explode('::', $value);

        return $constantParts[0];
    }
}
