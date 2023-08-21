<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\ArgumentBuilder;

use SprykerSdk\Integrator\Transfer\ClassMetadataTransfer;

interface ArgumentBuilderInterface
{
    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $classMetadataTransfer
     * @param bool $withSource
     *
     * @return array<\PhpParser\Node\Arg>
     */
    public function createAddPluginArguments(ClassMetadataTransfer $classMetadataTransfer, bool $withSource = true): array;

    /**
     * @param array<int, \SprykerSdk\Integrator\Transfer\ClassArgumentMetadataTransfer> $classArgumentMetadataTransfers
     *
     * @return array<\PhpParser\Node\Arg>
     */
    public function getArguments(array $classArgumentMetadataTransfers): array;
}
