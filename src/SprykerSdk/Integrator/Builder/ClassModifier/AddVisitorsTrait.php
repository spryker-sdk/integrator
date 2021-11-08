<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\ClassModifier;

use PhpParser\NodeTraverser;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;

trait AddVisitorsTrait
{
    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param array<\PhpParser\NodeVisitorAbstract> $visitors
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    protected function addVisitorsClassInformationTransfer(ClassInformationTransfer $classInformationTransfer, array $visitors): ClassInformationTransfer
    {
        $nodeTraverser = new NodeTraverser();
        foreach ($visitors as $visitor) {
            $nodeTraverser->addVisitor($visitor);
        }

        $classInformationTransfer->setClassTokenTree(
            $nodeTraverser->traverse($classInformationTransfer->getClassTokenTree())
        );

        return $classInformationTransfer;
    }
}
