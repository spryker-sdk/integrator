<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\FileStorage;

class FileStorageFactory
{
    /**
     * @var \SprykerSdk\Integrator\Builder\FileStorage\FileStorageInterface|null
     */
    protected $fileStorageInstance;

    /**
     * @var \SprykerSdk\Integrator\Builder\FileStorage\FileStorageFactory
     */
    protected static $instance;

    protected function __construct()
    {
    }

    /**
     * @return self
     */
    public static function getInstance(): self
    {
        if (static::$instance === null) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\FileStorage\FileStorageInterface
     */
    public function createEmptyFilesStorage(): FileStorageInterface
    {
        if ($this->fileStorageInstance === null) {
            $this->fileStorageInstance = new FileStorage();
        }

        return $this->fileStorageInstance;
    }
}
