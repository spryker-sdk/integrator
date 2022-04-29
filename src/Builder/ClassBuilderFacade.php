<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder;

use SprykerSdk\Integrator\IntegratorFactoryAwareTrait;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;

class ClassBuilderFacade implements ClassBuilderFacadeInterface
{
    use IntegratorFactoryAwareTrait;

    /**
     * {@inheritDoc}
     *
     * @param string $targetClassName
     * @param string $customOrganisation
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer|null
     */
    public function resolveClass(string $targetClassName, string $customOrganisation = ''): ?ClassInformationTransfer
    {
        return $this->getFactory()
            ->createClassResolver()
            ->resolveClass($targetClassName, $customOrganisation);
    }

    /**
     * {@inheritDoc}
     *
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     *
     * @return bool
     */
    public function storeClass(ClassInformationTransfer $classInformationTransfer): bool
    {
        return $this->getFactory()
            ->createClassFileWriter()
            ->storeClass($classInformationTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     *
     * @return string|null
     */
    public function printDiff(ClassInformationTransfer $classInformationTransfer): ?string
    {
        return $this->getFactory()
            ->createClassDiffPrinter()
            ->printDiff($classInformationTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $constantName
     * @param mixed $value
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function setConstant(ClassInformationTransfer $classInformationTransfer, string $constantName, $value): ClassInformationTransfer
    {
        return $this->getFactory()
            ->createClassConstantModifier()
            ->setConstant($classInformationTransfer, $constantName, $value);
    }

    /**
     * {@inheritDoc}
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
    ): ClassInformationTransfer {
        return $this->getFactory()
            ->createClassInstanceClassModifier()
            ->wireClassInstance($classInformationTransfer, $targetMethodName, $classNameToAdd, $before, $after, $index);
    }

    /**
     * {@inheritDoc}
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
    ): ?ClassInformationTransfer {
        return $this->getFactory()
            ->createClassInstanceClassModifier()
            ->unwireClassInstance($classInformationTransfer, $classNameToRemove, $targetMethodName);
    }

    /**
     * {@inheritDoc}
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
    ): ClassInformationTransfer {
        return $this->getFactory()
            ->createClassListModifier()
            ->wireClassConstant(
                $classInformationTransfer,
                $targetMethodName,
                $classNameToAdd,
                $constantName,
                $before,
                $after,
            );
    }

    /**
     * {@inheritDoc}
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
    ): ?ClassInformationTransfer {
        return $this->getFactory()
            ->createClassListModifier()
            ->unwireClassConstant($classInformationTransfer, $classNameToRemove, $targetMethodName);
    }

    /**
     * {@inheritDoc}
     *
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $methodName
     * @param array|string|float|int|bool|null $value
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function setMethodReturnValue(ClassInformationTransfer $classInformationTransfer, string $methodName, $value): ClassInformationTransfer
    {
        return $this->getFactory()
            ->createCommonClassModifier()
            ->setMethodReturnValue($classInformationTransfer, $methodName, $value);
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $targetMethodName
     * @param string $key
     * @param string $classNameToAdd
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function wireGlueRelationship(
        ClassInformationTransfer $classInformationTransfer,
        string $targetMethodName,
        string $key,
        string $classNameToAdd
    ): ClassInformationTransfer {
        return $this->getFactory()
            ->createGlueRelationshipModifier()
            ->wireGlueRelationship($classInformationTransfer, $targetMethodName, $key, $classNameToAdd);
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $targetMethodName
     * @param string $key
     * @param string $classNameToAdd
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function unwireGlueRelationship(
        ClassInformationTransfer $classInformationTransfer,
        string $targetMethodName,
        string $key,
        string $classNameToAdd
    ): ClassInformationTransfer {
        return $this->getFactory()
            ->createGlueRelationshipModifier()
            ->unwireGlueRelationship($classInformationTransfer, $targetMethodName, $key, $classNameToAdd);
    }
}
