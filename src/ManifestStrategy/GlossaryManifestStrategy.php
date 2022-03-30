<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\ManifestStrategy;

use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;

class GlossaryManifestStrategy extends AbstractManifestStrategy
{
    /**
     * @var string
     */
    protected const GLOSSARY_FILE_EXIST_ERROR = 'File `%s` does not exist. Please check file path or customize using `IntegratorConfig::getGlossaryFilePath()`.';

    /**
     * @var string
     */
    protected const GLOSSARY_FILE_SUCCESSFULLY_UPDATED = 'Glossary file `%s` successfully updated';

    /**
     * @var int
     */
    protected const GLOSSARY_LINE_PARTS_COUNT = 3;

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'glossary-key';
    }

    /**
     * @param array<string, array<string, string>> $manifest
     * @param string $moduleName
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $inputOutput
     * @param bool $isDry
     *
     * @return bool
     */
    public function apply(array $manifest, string $moduleName, InputOutputInterface $inputOutput, bool $isDry): bool
    {
        $glossaryFilePath = $this->config->getGlossaryFilePath();
        if (!file_exists($glossaryFilePath)) {
            $inputOutput->writeln(sprintf(
                static::GLOSSARY_FILE_EXIST_ERROR,
                $glossaryFilePath,
            ), InputOutputInterface::DEBUG);

            return false;
        }

        $existingGlossaryFileLines = $this->createGlossaryExistingFileLines();
        $updatingGlossaryFileLines = $this->getUpdatingGlossaryFileLines(
            $manifest,
            $existingGlossaryFileLines,
        );
        $resultGlossaryFileLines = array_merge($existingGlossaryFileLines, $updatingGlossaryFileLines);
        if (!$isDry) {
            file_put_contents($this->config->getGlossaryFilePath(), implode("\n", $resultGlossaryFileLines));
        }
        $inputOutput->writeln(sprintf(
            static::GLOSSARY_FILE_SUCCESSFULLY_UPDATED,
            $glossaryFilePath,
        ), InputOutputInterface::DEBUG);

        return true;
    }

    /**
     * @param array<string, array<string, string>> $manifest
     * @param array<array-key, string> $existingGlossaryFileLines
     *
     * @return array<array-key, string>
     */
    protected function getUpdatingGlossaryFileLines(
        array $manifest,
        array $existingGlossaryFileLines
    ): array {
        $mappedGlossaryLinesByGlossaryKeysAndLanguages = $this->createMappedGlossaryLinesByGlossaryKeysAndLanguages(
            $existingGlossaryFileLines,
        );
        $updatingGlossaryFileLines = [];
        foreach ($manifest as $manifestKey => $manifestValue) {
            $glossaryUpdatingFileLines = $this->getGlossaryFileLinesFromManifestKey(
                $manifestKey,
                $manifestValue,
                $mappedGlossaryLinesByGlossaryKeysAndLanguages,
            );
            $updatingGlossaryFileLines = array_merge($updatingGlossaryFileLines, $glossaryUpdatingFileLines);
        }

        return $updatingGlossaryFileLines;
    }

    /**
     * @param string $manifestKey
     * @param array<string, string> $manifestValue
     * @param array<string, array<string, string>> $mappedGlossaryLinesByGlossaryKeysAndLanguages
     *
     * @return array<array-key, string>
     */
    protected function getGlossaryFileLinesFromManifestKey(
        string $manifestKey,
        array $manifestValue,
        array $mappedGlossaryLinesByGlossaryKeysAndLanguages
    ): array {
        $glossaryUpdatingFileLines = [];
        foreach ($manifestValue as $keyLanguage => $keyValue) {
            if (isset($mappedGlossaryLinesByGlossaryKeysAndLanguages[$manifestKey][$keyLanguage])) {
                continue;
            }
            $glossaryUpdatingFileLines[] = $this->createGlossaryFileLine($manifestKey, $keyLanguage, $keyValue);
        }

        return $glossaryUpdatingFileLines;
    }

    /**
     * @param string $manifestKey
     * @param string $keyLanguage
     * @param string $keyValue
     *
     * @return string
     */
    protected function createGlossaryFileLine(string $manifestKey, string $keyLanguage, string $keyValue): string
    {
        return implode(';', [
            $manifestKey,
            $keyValue,
            $keyLanguage,
        ]);
    }

    /**
     * @param array<array-key, string> $glossaryExistingFileLines
     *
     * @return array<string, array<string, string>>
     */
    protected function createMappedGlossaryLinesByGlossaryKeysAndLanguages(array $glossaryExistingFileLines): array
    {
        $mappedGlossaryLinesByGlossaryKeysAndLanguages = [];
        foreach ($glossaryExistingFileLines as $glossaryLine) {
            $glossaryLineParts = explode(';', $glossaryLine);
            if (count($glossaryLineParts) != static::GLOSSARY_LINE_PARTS_COUNT) {
                continue;
            }
            $mappedGlossaryLinesByGlossaryKeysAndLanguages[$glossaryLineParts[0]][$glossaryLineParts[2]] = trim($glossaryLine);
        }

        return $mappedGlossaryLinesByGlossaryKeysAndLanguages;
    }

    /**
     * @return array<array-key, string>
     */
    protected function createGlossaryExistingFileLines(): array
    {
        $glossaryContent = file_get_contents($this->config->getGlossaryFilePath());
        if (!$glossaryContent) {
            return [];
        }

        return explode("\n", trim($glossaryContent));
    }
}
