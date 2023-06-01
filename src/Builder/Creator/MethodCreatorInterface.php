<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Creator;

use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;

interface MethodCreatorInterface
{
    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param array|string|float|int|bool|null $value
     * @param bool $isLiteral
     *
     * @return array<array-key, \PhpParser\Node\Stmt>
     */
    public function createMethodBody(ClassInformationTransfer $classInformationTransfer, $value, bool $isLiteral = false): array;

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $methodName
     * @param mixed $value
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function createMethod(
        ClassInformationTransfer $classInformationTransfer,
        string $methodName,
        $value
    ): ClassInformationTransfer;
}
