<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\ClassModifier;

use SprykerSdk\Integrator\Builder\Finder\ClassNodeFinderInterface;
use SprykerSdk\Integrator\Builder\Visitor\AddConstantVisitor;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;

class ClassConstantModifier implements ClassConstantModifierInterface
{
    use AddVisitorsTrait;

    /**
     * @var \SprykerSdk\Integrator\Builder\Finder\ClassNodeFinderInterface
     */
    protected $classNodeFinder;

    /**
     * @param \SprykerSdk\Integrator\Builder\Finder\ClassNodeFinderInterface $classNodeFinder
     */
    public function __construct(ClassNodeFinderInterface $classNodeFinder)
    {
        $this->classNodeFinder = $classNodeFinder;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $constantName
     * @param mixed $value
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function setConstant(ClassInformationTransfer $classInformationTransfer, string $constantName, $value): ClassInformationTransfer
    {
        $parentConstant = $this->classNodeFinder->findConstantNode($classInformationTransfer, $constantName);
        $modifier = 'public';
        if ($parentConstant) {
            if ($parentConstant->isProtected()) {
                $modifier = 'protected';
            } elseif ($parentConstant->isPrivate()) {
                $modifier = 'private';
            }
        }

        $visitors = [
            new AddConstantVisitor($constantName, $value, $modifier),
        ];

        return $this->addVisitorsClassInformationTransfer($classInformationTransfer, $visitors);
    }
}
