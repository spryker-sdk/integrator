<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Extractor;

interface ExpressionExtractorInterface
{
    /**
     * @param array<\PhpParser\Node> $syntaxTree
     *
     * @throws \RuntimeException
     *
     * @return array<string, string>
     */
    public function extractExpressions(array $syntaxTree): array;
}
