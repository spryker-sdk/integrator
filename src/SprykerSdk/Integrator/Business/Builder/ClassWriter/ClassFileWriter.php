<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerSdk\Integrator\Business\Builder\ClassWriter;

use Shared\Transfer\ClassInformationTransfer;
use SprykerSdk\Integrator\Business\Builder\Printer\ClassPrinter;

class ClassFileWriter
{
    /**
     * @var \SprykerSdk\Integrator\Business\Builder\Printer\ClassPrinter
     */
    protected $classPrinter;

    /**
     * @param \SprykerSdk\Integrator\Business\Builder\Printer\ClassPrinter $classPrinter
     */
    public function __construct(ClassPrinter $classPrinter)
    {
        $this->classPrinter = $classPrinter;
    }

    /**
     * @param \Shared\Transfer\ClassInformationTransfer $classInformationTransfer
     *
     * @return bool
     */
    public function storeClass(ClassInformationTransfer $classInformationTransfer): bool
    {
        if ($classInformationTransfer->getOriginalClassTokenTree()) {
            $code = $this->classPrinter->printFormatPreserving(
                $classInformationTransfer->getClassTokenTree(),
                $classInformationTransfer->getOriginalClassTokenTree(),
                $classInformationTransfer->getTokens()
            );
        } else {
            $code = $this->classPrinter->prettyPrintFile(
                $classInformationTransfer->getClassTokenTree()
            );
        }

        return $this->filePutContents($classInformationTransfer->getFilePath(), $code);
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
