<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Wire;

use PhpParser\Node\Stmt\ClassMethod;
use SprykerSdk\Integrator\Builder\ClassModifier\AddVisitorsTrait;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Applicable\ApplicableInterface;
use SprykerSdk\Integrator\Builder\Visitor\AddPluginToPluginListVisitor;
use SprykerSdk\Integrator\Builder\Visitor\AddUseVisitor;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;
use SprykerSdk\Integrator\Transfer\ClassMetadataTransfer;

class WireReturnArrayModifierStrategy implements WireModifierStrategyInterface
{
    use AddVisitorsTrait;

    /**
     * @var \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Applicable\ApplicableInterface
     */
    protected ApplicableInterface $applicableCheck;

    /**
     * @param \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Applicable\ApplicableInterface $applicableCheck
     */
    public function __construct(ApplicableInterface $applicableCheck)
    {
        $this->applicableCheck = $applicableCheck;
    }

    /**
     * @param \PhpParser\Node\Stmt\ClassMethod $node
     *
     * @return bool
     */
    public function isApplicable(ClassMethod $node): bool
    {
        return $this->applicableCheck->isApplicable($node);
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $classMetadataTransfer
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function wireClassInstance(
        ClassInformationTransfer $classInformationTransfer,
        ClassMetadataTransfer $classMetadataTransfer
    ): ClassInformationTransfer {
        $visitors = $this->getWireVisitors($classMetadataTransfer);

        if ($classMetadataTransfer->getIndex() !== null && $this->isIndexFullyQualifiedClassName($classMetadataTransfer->getIndex())) {
            $visitors[] = new AddUseVisitor($this->getFullyQualifiedClassNameFromIndex($classMetadataTransfer->getIndex()));
        }

        return $this->addVisitorsClassInformationTransfer($classInformationTransfer, $visitors);
    }

    /**
     * @param string $index
     *
     * @return bool
     */
    protected function isIndexFullyQualifiedClassName(string $index): bool
    {
        return strpos($index, '::') !== false && strpos($index, 'static::') === false;
    }

    /**
     * @param string $index
     *
     * @return string
     */
    protected function getFullyQualifiedClassNameFromIndex(string $index): string
    {
        return explode('::', $index)[0];
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $classMetadataTransfer
     *
     * @return array<\PhpParser\NodeVisitorAbstract>
     */
    protected function getWireVisitors(ClassMetadataTransfer $classMetadataTransfer): array
    {
        return [
            new AddUseVisitor($classMetadataTransfer->getSourceOrFail()),
            new AddPluginToPluginListVisitor(
                $classMetadataTransfer,
            ),
        ];
    }
}
