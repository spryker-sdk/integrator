<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\FileStorage;

class FileStorage implements FileStorageInterface
{
    /**
     * @var array<string>
     */
    protected $filePaths;

    /**
     * @param array<string> $filePaths
     */
    public function __construct(array $filePaths = [])
    {
        $this->filePaths = $filePaths;
    }

    /**
     * @param string $filePath
     *
     * @return void
     */
    public function addFile(string $filePath): void
    {
        if (in_array($filePath, $this->filePaths, true)) {
            return;
        }

        $this->filePaths[] = $filePath;
    }

    /**
     * @return array<string>
     */
    public function flush(): array
    {
        $filePaths = $this->filePaths;

        $this->filePaths = [];

        return $filePaths;
    }
}
