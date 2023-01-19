<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\FileNormalizer;

use SprykerSdk\Integrator\Builder\FileStorage\FileStorageInterface;
use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;

class FileNormalizersExecutor implements FileNormalizersExecutorInterface
{
    /**
     * @var \SprykerSdk\Integrator\Builder\FileStorage\FileStorageInterface
     */
    protected $fileStorage;

    /**
     * @var iterable<\SprykerSdk\Integrator\Builder\FileNormalizer\FileNormalizerInterface>
     */
    protected $fileNormalizers;

    /**
     * @param \SprykerSdk\Integrator\Builder\FileStorage\FileStorageInterface $fileStorage
     * @param iterable<\SprykerSdk\Integrator\Builder\FileNormalizer\FileNormalizerInterface> $fileNormalizers
     */
    public function __construct(FileStorageInterface $fileStorage, iterable $fileNormalizers)
    {
        $this->fileStorage = $fileStorage;
        $this->fileNormalizers = $fileNormalizers;
    }

    /**
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $inputOutput
     * @param bool $isDry
     *
     * @return void
     */
    public function execute(InputOutputInterface $inputOutput, bool $isDry): void
    {
        $files = $this->fileStorage->flush();

        if (count($files) === 0) {
            return;
        }

        foreach ($this->fileNormalizers as $fileNormalizer) {
            if (!$fileNormalizer->isApplicable()) {
                continue;
            }

            if ($isDry) {
                $inputOutput->writeln(sprintf('Executing %s normalizer', get_class($fileNormalizer)));

                continue;
            }

            $fileNormalizer->normalize($files);
        }
    }
}