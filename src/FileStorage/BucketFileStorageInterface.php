<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\FileStorage;

interface BucketFileStorageInterface
{
    /**
     * @param string $filePath
     * @param string $fileData
     *
     * @return void
     */
    public function addFile(string $filePath, string $fileData): void;

    /**
     * @param string $filePath
     *
     * @return string|null
     */
    public function getFile(string $filePath): ?string;
}
