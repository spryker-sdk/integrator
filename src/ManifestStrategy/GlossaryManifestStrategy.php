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
    protected const GLOSSARY_FILE_EXIST_ERROR = 'File %s does not exist. Please check filepath.';

    /**
     * @var string
     */
    protected const GLOSSARY_KEY_ADD_DEBUG_MESSAGE = 'Glossary key %s was added to glossary file %s';

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

        foreach ($manifest as $manifestKey => $manifestValue) {
            $this->writeManifestKeyToGlossaryFile($manifestKey, $manifestValue, $isDry);
            $inputOutput->writeln(sprintf(
                static::GLOSSARY_KEY_ADD_DEBUG_MESSAGE,
                $manifestKey,
                $glossaryFilePath,
            ), InputOutputInterface::DEBUG);
        }

        return true;
    }

    /**
     * @param string $manifestKey
     * @param array<string, string> $manifestValue
     * @param bool $isDry
     *
     * @return void
     */
    protected function writeManifestKeyToGlossaryFile(string $manifestKey, array $manifestValue, bool $isDry): void
    {
        if ($isDry) {
            return;
        }
        foreach ($manifestValue as $keyLanguage => $keyValue) {
            $glossaryFileLine = $this->createGlossaryFileLine($manifestKey, $keyLanguage, $keyValue);
            file_put_contents($this->config->getGlossaryFilePath(), $glossaryFileLine, FILE_APPEND);
        }
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
        ]) . "\n";
    }
}
