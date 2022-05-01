<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder;

use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;

interface ClassBuilderFacadeInterface
{
    /**
     * Specification:
     * - Resolves class path from the name and organization.
     *
     * @param string $targetClassName
     * @param string $customOrganisation
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer|null
     */
    public function resolveClass(string $targetClassName, string $customOrganisation = ''): ?ClassInformationTransfer;

    /**
     * Specification:
     * - Stores class to file basing on given parameters.
     *
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     *
     * @return bool
     */
    public function storeClass(ClassInformationTransfer $classInformationTransfer): bool;

    /**
     * Specification:
     * - Prints diff between classes.
     *
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     *
     * @return string|null
     */
    public function printDiff(ClassInformationTransfer $classInformationTransfer): ?string;

    /**
     * Specification:
     * - Sets constant value basing on the input given.
     *
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $constantName
     * @param mixed $value
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function setConstant(ClassInformationTransfer $classInformationTransfer, string $constantName, $value): ClassInformationTransfer;

    /**
     * Specification:
     * - Wires an instance of given class with given content.
     *
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $targetMethodName
     * @param string $classNameToAdd
     * @param string $before
     * @param string $after
     * @param string|null $index
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function wireClassInstance(
        ClassInformationTransfer $classInformationTransfer,
        string $targetMethodName,
        string $classNameToAdd,
        string $before = '',
        string $after = '',
        ?string $index = null
    ): ClassInformationTransfer;

    /**
     * Specification:
     * - Removes given method from a given class.
     *
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $classNameToRemove
     * @param string $targetMethodName
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer|null
     */
    public function unwireClassInstance(
        ClassInformationTransfer $classInformationTransfer,
        string $classNameToRemove,
        string $targetMethodName
    ): ?ClassInformationTransfer;

    /**
     * Specification:
     * - Adds a constant to a class.
     *
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $targetMethodName
     * @param string $classNameToAdd
     * @param string $constantName
     * @param string $before
     * @param string $after
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function wireClassConstant(
        ClassInformationTransfer $classInformationTransfer,
        string $targetMethodName,
        string $classNameToAdd,
        string $constantName,
        string $before = '',
        string $after = ''
    ): ClassInformationTransfer;

    /**
     * Specification:
     * - Removes a constant from class.
     *
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $classNameToRemove
     * @param string $targetMethodName
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer|null
     */
    public function unwireClassConstant(
        ClassInformationTransfer $classInformationTransfer,
        string $classNameToRemove,
        string $targetMethodName
    ): ?ClassInformationTransfer;

    /**
     * Specification:
     * - Creates or updates existing class method by value.
     * - Sets a return value of the method.
     * - Sets doc block of new created method.
     *
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $methodName
     * @param array|string|float|int|bool|null $value
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
