<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator\ManifestStrategy;

use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\IntegratorConfig;
use Symfony\Component\Process\Process;

class ExecuteConsoleManifestStrategy extends AbstractManifestStrategy
{
    /**
     * @return string
     */
    public function getType(): string
    {
        return 'execute-console';
    }

    /**
     * @param string[] $manifest
     * @param string $moduleName
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $inputOutput
     * @param bool $isDry
     *
     * @return bool
     */
    public function apply(array $manifest, string $moduleName, InputOutputInterface $inputOutput, bool $isDry): bool
    {
        $target = $manifest[IntegratorConfig::MANIFEST_KEY_TARGET];
        $process = $this->getProcess(explode(' ', $target));

        $statusCode = 0;
        if (!$isDry) {
            $statusCode = $process->run();
        }

        return !$statusCode;
    }

    /**
     * @param array $command
     *
     * @return \Symfony\Component\Process\Process
     */
    protected function getProcess(array $command): Process
    {
        return new Process($command, $this->config->getProjectRootDirectory(), null, null, 0);
    }
}
