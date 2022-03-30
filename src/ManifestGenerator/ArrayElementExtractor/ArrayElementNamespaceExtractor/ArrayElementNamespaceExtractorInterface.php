<?php

namespace SprykerSdk\Integrator\ManifestGenerator\ArrayElementExtractor\ArrayElementNamespaceExtractor;

interface ArrayElementNamespaceExtractorInterface
{
    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function isApplicable($value): bool;

    /**
     * @param mixed $value
     *
     * @return string|null
     */
    public function extractNamespace($value): ?string;
}
