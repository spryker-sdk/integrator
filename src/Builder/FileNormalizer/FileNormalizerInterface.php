<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\FileNormalizer;

interface FileNormalizerInterface
{
    /**
     * @param array<string> $filePaths
     *
     * @return void
     */
    public function normalize(array $filePaths): void;

    /**
     * @return bool
     */
    public function isApplicable(): bool;

    /**
     * @return string|null
     */
    public function getErrorMessage(): ?string;
}
