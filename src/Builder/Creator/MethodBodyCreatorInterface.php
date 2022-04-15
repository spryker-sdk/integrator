<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Creator;

use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;

interface MethodBodyCreatorInterface
{
    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param array|string|float|int|bool|null $value
     *
     * @return array<array-key, \PhpParser\Node\Stmt>
     */
    public function createMethodBody(ClassInformationTransfer $classInformationTransfer, $value): array;
}
