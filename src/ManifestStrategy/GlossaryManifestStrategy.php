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
                'File `%s` does not exist. Please check file path or customize using `IntegratorConfig::getGlossaryFilePath()`.',
                $glossaryFilePath,
            ), InputOutputInterface::DEBUG);

            return false;
        }

        $existingGlossaryFileLines = $this->getGlossaryExistingFileLines();
        $glossaryFileLinesForAdd = $this->getGlossaryFileLinesForAdd(
            $manifest,
            $existingGlossaryFileLines,
        );
        $resultGlossaryFileLines = array_merge($existingGlossaryFileLines, $glossaryFileLinesForAdd);
        if (!$isDry) {
            file_put_contents($this->config->getGlossaryFilePath(), implode("\n", $resultGlossaryFileLines));
        }
        $inputOutput->writeln(sprintf(
            'Glossary file `%s` successfully updated',
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
    protected function getGlossaryFileLinesForAdd(
        array $manifest,
        array $existingGlossaryFileLines
    ): array {
        $indexGlossaryLinesByGlossaryKeysAndLanguages = $this->indexGlossaryLinesByGlossaryKeysAndLanguages(
            $existingGlossaryFileLines,
        );
        $glossaryFileLinesForAdd = [];
        foreach ($manifest as $glossaryKey => $glossaryValues) {
            $glossaryKeyFileLinesForAdd = $this->getGlossaryKeyFileLinesFromGlossaryKey(
                $glossaryKey,
                $glossaryValues,
                $indexGlossaryLinesByGlossaryKeysAndLanguages,
            );
            $glossaryFileLinesForAdd = array_merge($glossaryFileLinesForAdd, $glossaryKeyFileLinesForAdd);
        }

        return $glossaryFileLinesForAdd;
    }

    /**
     * @param string $glossaryKey
     * @param array<string, string> $glossaryValues
     * @param array<string, array<string, string>> $indexGlossaryLinesByGlossaryKeysAndLanguages
     *
     * @return array<array-key, string>
     */
    protected function getGlossaryKeyFileLinesFromGlossaryKey(
        string $glossaryKey,
        array $glossaryValues,
        array $indexGlossaryLinesByGlossaryKeysAndLanguages
    ): array {
        $glossaryKeyFileLines = [];
        foreach ($glossaryValues as $glossaryKeyLanguage => $glossaryKeyValue) {
            if (isset($indexGlossaryLinesByGlossaryKeysAndLanguages[$glossaryKey][$glossaryKeyLanguage])) {
                continue;
            }
            $glossaryKeyFileLines[] = $this->createGlossaryFileLine(
                $glossaryKey,
                $glossaryKeyLanguage,
                $glossaryKeyValue,
            );
        }

        return $glossaryKeyFileLines;
    }

    /**
     * @param string $glossaryKey
     * @param string $glossaryKeyLanguage
     * @param string $glossaryKeyValue
     *
     * @return string
     */
    protected function createGlossaryFileLine(string $glossaryKey, string $glossaryKeyLanguage, string $glossaryKeyValue): string
    {
        return implode(',', [
            $glossaryKey,
            $glossaryKeyValue,
            $glossaryKeyLanguage,
        ]);
    }

    /**
     * @param array<array-key, string> $glossaryExistingFileLines
     *
     * @return array<string, array<string, string>>
     */
    protected function indexGlossaryLinesByGlossaryKeysAndLanguages(array $glossaryExistingFileLines): array
    {
        $indexGlossaryLinesByGlossaryKeysAndLanguages = [];
        foreach ($glossaryExistingFileLines as $glossaryLine) {
            $glossaryLineParts = explode(',', $glossaryLine);
            if (count($glossaryLineParts) !== static::GLOSSARY_LINE_PARTS_COUNT) {
                continue;
            }
            $indexGlossaryLinesByGlossaryKeysAndLanguages[$glossaryLineParts[0]][$glossaryLineParts[2]] = trim($glossaryLine);
        }

        return $indexGlossaryLinesByGlossaryKeysAndLanguages;
    }

    /**
     * @return array<array-key, string>
     */
    protected function getGlossaryExistingFileLines(): array
    {
        $glossaryContent = file_get_contents($this->config->getGlossaryFilePath());
        if (!$glossaryContent) {
            return [];
        }

        return explode(PHP_EOL, trim($glossaryContent));
    }
}
