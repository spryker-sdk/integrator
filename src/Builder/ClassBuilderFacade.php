<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder;

use SprykerSdk\Integrator\IntegratorFactoryAwareTrait;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;
use SprykerSdk\Integrator\Transfer\ClassMetadataTransfer;

class ClassBuilderFacade implements ClassBuilderFacadeInterface
{
    use IntegratorFactoryAwareTrait;

    /**
     * {@inheritDoc}
     *
     * @param string $targetClassName
     * @param string $customOrganisation
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function resolveClass(string $targetClassName, string $customOrganisation = ''): ClassInformationTransfer
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
            ->createFileWriter()
            ->storeFile($classInformationTransfer);
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
     * @param bool $isLiteral
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function setConstant(
        ClassInformationTransfer $classInformationTransfer,
        string $constantName,
        $value,
        bool $isLiteral = false
    ): ClassInformationTransfer {
        return $this->getFactory()
            ->createClassConstantModifier()
            ->setConstant($classInformationTransfer, $constantName, $value, $isLiteral);
    }

    /**
     * {@inheritDoc}
     *
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $classMetadataTransfer
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function wireClassInstance(
        ClassInformationTransfer $classInformationTransfer,
        ClassMetadataTransfer $classMetadataTransfer
    ): ClassInformationTransfer {
        return $this->getFactory()
            ->createWireClassInstanceModifier()
            ->wire($classInformationTransfer, $classMetadataTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $classMetadataTransfer
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer|null
     */
    public function unwireClassInstance(
        ClassInformationTransfer $classInformationTransfer,
        ClassMetadataTransfer $classMetadataTransfer
    ): ?ClassInformationTransfer {
        return $this->getFactory()
            ->createUnwireClassInstanceModifier()
            ->unwire($classInformationTransfer, $classMetadataTransfer);
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
            ->createWireClassConstantModifier()
            ->wire(
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
            ->createUnwireClassConstantModifier()
            ->unwire($classInformationTransfer, $classNameToRemove, $targetMethodName);
    }

    /**
     * {@inheritDoc}
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
    ): ClassInformationTransfer {
        return $this->getFactory()
            ->createCommonClassModifier()
            ->createClassMethod($classInformationTransfer, $methodName, $value, $isLiteral, $previousValue);
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
            ->createWireGlueRelationshipModifier()
            ->wire($classInformationTransfer, $targetMethodName, $key, $classNameToAdd);
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
            ->createUnwireGlueRelationshipModifier()
            ->unwire($classInformationTransfer, $targetMethodName, $key, $classNameToAdd);
    }
}
