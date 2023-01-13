<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\FileNormalizer;

use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;

interface FileNormalizersExecutorInterface
{
    /**
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $inputOutput
     * @param bool $isDry
     *
     * @return void
     */
    public function execute(InputOutputInterface $inputOutput, bool $isDry): void;
}
