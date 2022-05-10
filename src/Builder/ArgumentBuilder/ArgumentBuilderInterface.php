<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator\Builder\ArgumentBuilder;

interface ArgumentBuilderInterface
{
    /**
     * @param array<int, \SprykerSdk\Integrator\Transfer\ClassArgumentMetadataTransfer> $classArgumentMetadataTransfers
     *
     * @return array<\PhpParser\Node\Arg>
     */
    public function getArguments(array $classArgumentMetadataTransfers): array;
}
