<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\ClassModifier;

use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;

interface CommonClassModifierInterface
{
    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $targetMethodName
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function overrideMethodFromParent(ClassInformationTransfer $classInformationTransfer, string $targetMethodName): ClassInformationTransfer;

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $targetMethodName
     * @param array<string, \PhpParser\Node> $methodNodeProperties
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function replaceMethodBody(
        ClassInformationTransfer $classInformationTransfer,
        string $targetMethodName,
        array $methodNodeProperties = []
    ): ClassInformationTransfer;

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $methodNameToRemove
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function removeClassMethod(ClassInformationTransfer $classInformationTransfer, string $methodNameToRemove): ClassInformationTransfer;

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $methodName
     * @param mixed $value
     * @param bool $isLiteral
     * @param mixed $previousValue
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function createClassMethod(
        ClassInformationTransfer $classInformationTransfer,
        string $methodName,
        $value,
        bool $isLiteral,
        $previousValue
    ): ClassInformationTransfer;
}
