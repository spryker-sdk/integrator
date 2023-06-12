<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\FileNormalizer;

use RuntimeException;

class TestPhpCSFixerFileNormalizer extends PhpCSFixerFileNormalizer
{
    /**
     * @var string
     */
    protected const CS_RULES_STANDARD = 'Spryker';

    /**
     * @return bool
     */
    public function isApplicable(): bool
    {
        return $this->config->isTestEnvironment()
            && is_file($this->config->getIntegratorRootPath() . '/phpcs.xml')
            && is_file($this->getCSFixPath());
    }

    /**
     * @param array $filePaths
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    public function normalize(array $filePaths): void
    {
        $process = $this->processExecutor->execute([$this->getCSFixPath(), sprintf('--standard=%s', static::CS_RULES_STANDARD), ...$this->getAbsoluteFilePaths($filePaths)]);
        if ($process->getExitCode() > 0 && $process->getErrorOutput() !== '') {
            throw new RuntimeException($process->getErrorOutput());
        }
    }

    /**
     * @return string
     */
    protected function getCSFixPath(): string
    {
        return $this->config->getIntegratorRootPath() . '/' . static::PHP_CS_FIX_RELATIVE_PATH;
    }
}
