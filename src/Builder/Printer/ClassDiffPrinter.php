<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Printer;

use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\DiffOnlyOutputBuilder;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;

class ClassDiffPrinter implements ClassDiffPrinterInterface
{
    /**
     * @var \SprykerSdk\Integrator\Builder\Printer\ClassPrinter
     */
    protected $classPrinter;

    /**
     * @param \SprykerSdk\Integrator\Builder\Printer\ClassPrinter $classPrinter
     */
    public function __construct(ClassPrinter $classPrinter)
    {
        $this->classPrinter = $classPrinter;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     *
     * @return string
     */
    public function printDiff(ClassInformationTransfer $classInformationTransfer): string
    {
        $originalCode = '';
        if ($classInformationTransfer->getOriginalClassTokenTree()) {
            $code = $this->classPrinter->printFormatPreserving(
                $classInformationTransfer->getClassTokenTree(),
                $classInformationTransfer->getOriginalClassTokenTree(),
                $classInformationTransfer->getTokens(),
            );
            $originalCode = $this->classPrinter->printFormatPreserving(
                $classInformationTransfer->getOriginalClassTokenTree(),
                $classInformationTransfer->getOriginalClassTokenTree(),
                $classInformationTransfer->getTokens(),
            );
        } else {
            $code = $this->classPrinter->prettyPrintFile(
                $classInformationTransfer->getClassTokenTree(),
            );
        }

        $builder = new DiffOnlyOutputBuilder(
            "--- Original\n+++ New\n",
        );
        $differ = (new Differ($builder));
        $diff = $differ->diff(
            $originalCode,
            $code,
        );

        if ($diff === "--- Original\n+++ New\n") {
            return '';
        }

        return $diff;
    }
}
