<?php

namespace SprykerSdk\Integrator\ManifestGenerator;

use League\Csv\Reader;
use SprykerSdk\Integrator\Common\UtilText\Filter\SeparatorToCamelCase;

class GlossaryKeyManifestStrategy extends AbstractManifestStrategy implements ManifestStrategyInterface
{
    /**
     * @var string
     */
    public const MANIFEST_KEY = 'glossary-key';

    /**
     * @var string
     */
    public const MANIFEST_KEY_NEW = 'new';

    /**
     * @param string $fileName
     *
     * @return bool
     */
    public function isApplicable(string $fileName): bool
    {
        return (bool)preg_match('#/glossary\.csv#', $fileName);
    }

    /**
     * @param string $fileName
     * @param string $originalFileName
     * @param array<string, array> $existingChanges
     *
     * @return array<string, array>
     */
    public function generateManifestData(string $fileName, string $originalFileName, array $existingChanges = []): array
    {
        $glossaryKeysAfter = $this->parseCsvFile($fileName);
        $glossaryKeyBefore = [];

        if (file_exists($originalFileName)) {
            $glossaryKeyBefore = $this->parseCsvFile($originalFileName);
        }

        $data = $this->compareFiles($glossaryKeyBefore, $glossaryKeysAfter);

        return $this->buildManifestArray($data, $existingChanges);
    }

    /**
     * @param string $fileName
     *
     * @return array<string, array<string, string>>
     */
    protected function parseCsvFile(string $fileName): array
    {
        $csv = Reader::createFromPath($fileName);

        $rows = [];
        foreach ($csv as $row) {
            $rows[$row[0]][$row[2]] = $row[1];
        }

        return $rows;
    }

    /**
     * @param array<string, array<string, string>> $before
     * @param array<string, array<string, string>> $after
     *
     * @return array<string, array>
     */
    protected function compareFiles(array $before, array $after): array
    {
        $newKeys = [];
        foreach ($after as $key => $data) {
            if (!isset($before[$key])) {
                $newKeys[] = $key;
            }
        }

        if (!$newKeys) {
            return [];
        }

        return $this->gatherGlossaryKeys($before, $after, $newKeys);
    }

    /**
     * @param array<string, array<string, string>> $before
     * @param array<string, array<string, string>> $after
     * @param array<string> $newKeys
     *
     * @return array<string, array>
     */
    protected function gatherGlossaryKeys(array $before, array $after, array $newKeys): array
    {
        $keys = [];

        foreach ($newKeys as $newKey) {
            $keys[$newKey] = $after[$newKey];
        }

        return $keys;
    }

    /**
     * @param array<string, array> $data
     * @param array<string, array> $manifests
     *
     * @return array<string, array>
     */
    protected function buildManifestArray(array $data, array $manifests): array
    {
        foreach ($data as $key => $changes) {
            [$organization, $module] = $this->getOrganizationAndModuleNameFromGlossaryKey($key);
            $manifests[$organization . '.' . $module][static::MANIFEST_KEY][static::MANIFEST_KEY_NEW][$key] = $changes;
        }

        return $manifests;
    }

    /**
     * @param string $key
     *
     * @return array<string>
     */
    protected function getOrganizationAndModuleNameFromGlossaryKey(string $key): array
    {
        $pos = strpos($key, '.');
        $moduleName = null;
        if ($pos) {
            $separatorToCamelCaseFilter = new SeparatorToCamelCase();
            $moduleName = $separatorToCamelCaseFilter->filter(substr($key, 0, $pos));
        }

        $namespace = '?';

        return [$namespace, $moduleName ?: '?'];
    }
}
