<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\ClassModifier\GlueRelationship\Unwire;

use RuntimeException;
use SprykerSdk\Integrator\Builder\ClassModifier\GlueRelationship\AbstractGlueRelationshipModifier;
use SprykerSdk\Integrator\Builder\Visitor\RemoveGlueRelationshipFromClassListVisitor;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;

class UnwireGlueRelationshipModifier extends AbstractGlueRelationshipModifier implements UnwireGlueRelationshipModifierInterface
{
    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $targetMethodName
     * @param string $key
     * @param string $classNameToRemove
     *
     * @throws \RuntimeException
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function unwire(
        ClassInformationTransfer $classInformationTransfer,
        string $targetMethodName,
        string $key,
        string $classNameToRemove
    ): ClassInformationTransfer {
        $methodNode = $this->classNodeFinder->findMethodNode($classInformationTransfer, $targetMethodName);
        if (!$methodNode) {
            $classInformationTransfer = $this->commonClassModifier->overrideMethodFromParent($classInformationTransfer, $targetMethodName);
            $methodNode = $this->classNodeFinder->findMethodNode($classInformationTransfer, $targetMethodName);
        }

        if (!$methodNode) {
            throw new RuntimeException('No method node found');
        }

        if (count($methodNode->params) !== 1) {
            throw new RuntimeException('Glue relationship method is not valid!');
        }

        if (!$this->isRelationshipExists($methodNode, $key, $classNameToRemove)) {
            return $classInformationTransfer;
        }

        [$keyClass, $keyConst] = explode('::', $key);
        $this->nodeTraverser->addVisitor(new RemoveGlueRelationshipFromClassListVisitor($targetMethodName, $keyClass, $keyConst, $classNameToRemove));
        $classInformationTransfer->setTokenTree($this->nodeTraverser->traverse($classInformationTransfer->getTokenTree()));

        return $classInformationTransfer;
    }
}
