<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\FileNormalizer;

class CodeSnifferCompositeNormalizer implements FileNormalizerInterface
{
    /**
     * @var array<\SprykerSdk\Integrator\Builder\FileNormalizer\FileNormalizerInterface>
     */
    protected array $codeSniffNormalizers;

    /**
     * @param array<\SprykerSdk\Integrator\Builder\FileNormalizer\FileNormalizerInterface> $codeSniffNormalizers
     */
    public function __construct(array $codeSniffNormalizers)
    {
        $this->codeSniffNormalizers = $codeSniffNormalizers;
    }

    /**
     * @return bool
     */
    public function isApplicable(): bool
    {
        return count(array_filter($this->codeSniffNormalizers, static fn (FileNormalizerInterface $normalizer): bool => $normalizer->isApplicable())) > 0;
    }

    /**
     * @return string|null
     */
    public function getErrorMessage(): ?string
    {
        return 'Unable to execute code fixer. Please manually execute it to adjust project code styles.';
    }

    /**
     * @param array $filePaths
     *
     * @return void
     */
    public function normalize(array $filePaths): void
    {
        foreach ($this->codeSniffNormalizers as $codeSniffNormalizer) {
            if (!$codeSniffNormalizer->isApplicable()) {
                continue;
            }

            $codeSniffNormalizer->normalize($filePaths);
        }
    }
}
