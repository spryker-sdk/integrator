<?php

namespace SprykerSdk\Integrator\ManifestGenerator;

interface ManifestStrategyInterface
{
    /**
     * @param string $fileName
     *
     * @return bool
     */
    public function isApplicable(string $fileName): bool;

    /**
     * @param string $fileName
     * @param string $originalFileName
     * @param array<string, array> $existingChanges
     *
     * @return array<string, array>
     */
    public function generateManifestData(string $fileName, string $originalFileName, array $existingChanges = []): array;
}
