<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\FileNormalizer;

use SprykerSdk\Integrator\Composer\ComposerLockReaderInterface;
use SprykerSdk\Integrator\IntegratorConfig;
use Symfony\Component\Filesystem\Filesystem;

class CodeSnifferCompositeNormalizer implements FileNormalizerInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Unable to execute code style fixer. Please manually execute it to adjust project code styles.';

    /**
     * @var string
     */
    protected const SPRYKER_CS_PACKAGE = 'spryker/code-sniffer';

    /**
     * @var string
     */
    protected const PHP_CS_FIXER_PATH = 'vendor/bin/phpcbf';

    /**
     * @var string
     */
    protected const PHP_CS_FIXER_CONFIG_PATH = 'phpcs.xml';

    /**
     * @var string
     */
    protected const INTERNAL_PHP_CS_FIXER_CONFIG_PATH = 'resources/phpcs.xml';

    /**
     * @var array<\SprykerSdk\Integrator\Builder\FileNormalizer\FileNormalizerInterface>
     */
    protected array $codeSniffNormalizers;

    /**
     * @var \SprykerSdk\Integrator\Composer\ComposerLockReaderInterface
     */
    protected ComposerLockReaderInterface $composerLockReader;

    /**
     * @var \SprykerSdk\Integrator\IntegratorConfig
     */
    protected IntegratorConfig $config;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected Filesystem $filesystem;

    /**
     * @param array $codeSniffNormalizers
     * @param \SprykerSdk\Integrator\Composer\ComposerLockReaderInterface $composerLockReader
     * @param \SprykerSdk\Integrator\IntegratorConfig $config
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     */
    public function __construct(
        array $codeSniffNormalizers,
        ComposerLockReaderInterface $composerLockReader,
        IntegratorConfig $config,
        Filesystem $filesystem
    ) {
        $this->codeSniffNormalizers = $codeSniffNormalizers;
        $this->composerLockReader = $composerLockReader;
        $this->config = $config;
        $this->filesystem = $filesystem;
    }

    /**
     * @return bool
     */
    public function isApplicable(): bool
    {
        return true;
    }

    /**
     * @return string|null
     */
    public function getErrorMessage(): ?string
    {
        return static::ERROR_MESSAGE;
    }

    /**
     * @param array $filePaths
     *
     * @return void
     */
    public function normalize(array $filePaths): void
    {
        if (!$this->isPhpCsConfigMissed()) {
            $this->executeNormalizers($filePaths);

            return;
        }

        $this->filesystem->copy($this->getInitialCsFixerConfig(), $this->getProjectCSFixerConfigPath());

        try {
            $this->executeNormalizers($filePaths);
        } finally {
            $this->filesystem->remove($this->getProjectCSFixerConfigPath());
        }
    }

    /**
     * @param array $filePaths
     *
     * @return void
     */
    protected function executeNormalizers(array $filePaths): void
    {
        foreach ($this->codeSniffNormalizers as $codeSniffNormalizer) {
            if (!$codeSniffNormalizer->isApplicable()) {
                continue;
            }

            $codeSniffNormalizer->normalize($filePaths);
        }
    }

    /**
     * @return bool
     */
    protected function isPhpCsConfigMissed(): bool
    {
        return $this->composerLockReader->getPackageData(static::SPRYKER_CS_PACKAGE) !== null
            && $this->filesystem->exists($this->getProjectCSFixerPath())
            && !$this->filesystem->exists($this->getProjectCSFixerConfigPath());
    }

    /**
     * @return string
     */
    protected function getProjectCSFixerPath(): string
    {
        return $this->config->getProjectRootDirectory() . static::PHP_CS_FIXER_PATH;
    }

    /**
     * @return string
     */
    protected function getProjectCSFixerConfigPath(): string
    {
        return $this->config->getProjectRootDirectory() . static::PHP_CS_FIXER_CONFIG_PATH;
    }

    /**
     * @return string
     */
    protected function getInitialCsFixerConfig(): string
    {
        return dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . static::INTERNAL_PHP_CS_FIXER_CONFIG_PATH;
    }
}
