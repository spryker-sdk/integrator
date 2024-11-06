<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\ClassLoader;

use SprykerSdk\Integrator\Transfer\FileInformationTransfer;

class FileLoader extends AbstractLoader implements FileLoaderInterface
{
    /**
     * @param string $path
     *
     * @return \SprykerSdk\Integrator\Transfer\FileInformationTransfer
     */
    public function loadFile(string $path): FileInformationTransfer
    {
        $fileInformationTransfer = (new FileInformationTransfer())->setFilePath($path);
        if (!file_exists($path)) {
            return $fileInformationTransfer;
        }

        $fileContents = file_get_contents($path);
        if (!$fileContents) {
            return $fileInformationTransfer;
        }
        $fileInformationTransfer->setContent($fileContents);

        $originalSyntaxTree = $this->parser->parse($fileContents);
        $syntaxTree = $originalSyntaxTree ? $this->traverseOriginalSyntaxTree($originalSyntaxTree) : [];

        $fileInformationTransfer->setTokenTree($syntaxTree)
            ->setOriginalTokenTree($originalSyntaxTree)
            ->setTokens($this->parser->getTokens());

        return $fileInformationTransfer;
    }
}
