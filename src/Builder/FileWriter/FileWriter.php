<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\FileWriter;

use SprykerSdk\Integrator\Builder\FileStorage\FileStorageInterface;
use SprykerSdk\Integrator\Builder\Printer\ClassPrinter;
use SprykerSdk\Integrator\Transfer\FileInformationTransfer;

class FileWriter implements FileWriterInterface
{
    /**
     * @var \SprykerSdk\Integrator\Builder\Printer\ClassPrinter
     */
    protected $classPrinter;

    /**
     * @var \SprykerSdk\Integrator\Builder\FileStorage\FileStorageInterface
     */
    protected $fileStorage;

    /**
     * @param \SprykerSdk\Integrator\Builder\Printer\ClassPrinter $classPrinter
     * @param \SprykerSdk\Integrator\Builder\FileStorage\FileStorageInterface $fileStorage
     */
    public function __construct(ClassPrinter $classPrinter, FileStorageInterface $fileStorage)
    {
        $this->classPrinter = $classPrinter;
        $this->fileStorage = $fileStorage;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\FileInformationTransfer $classInformationTransfer
     *
     * @return bool
     */
    public function storeFile(FileInformationTransfer $classInformationTransfer): bool
    {
        if ($classInformationTransfer->getOriginalTokenTree()) {
            $code = $this->classPrinter->printFormatPreserving(
                $classInformationTransfer->getTokenTree(),
                $classInformationTransfer->getOriginalTokenTree(),
                $classInformationTransfer->getTokens(),
            );
        } else {
            $code = $this->classPrinter->prettyPrintFile(
                $classInformationTransfer->getTokenTree(),
            );
        }

        $this->fileStorage->addFile($classInformationTransfer->getFilePathOrFail());

        return $this->filePutContents($classInformationTransfer->getFilePathOrFail(), $code);
    }

    /**
     * @param string $fullPath
     * @param string $contents
     *
     * @return bool
     */
    protected function filePutContents(string $fullPath, string $contents): bool
    {
        $parts = explode('/', $fullPath);
        array_pop($parts);
        $dir = implode('/', $parts);

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        return (bool)file_put_contents($fullPath, $contents);
    }
}
