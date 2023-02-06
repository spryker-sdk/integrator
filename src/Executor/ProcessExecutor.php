<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Executor;

use Symfony\Component\Process\Process;

class ProcessExecutor implements ProcessExecutorInterface
{
    /**
     * @param array<string> $command
     * @param int|null $timeout
     *
     * @return \Symfony\Component\Process\Process
     */
    public function execute(array $command, ?int $timeout = 300): Process
    {
        $process = new Process($command);
        $process->setTimeout($timeout);
        $process->run();

        return $process;
    }
}
