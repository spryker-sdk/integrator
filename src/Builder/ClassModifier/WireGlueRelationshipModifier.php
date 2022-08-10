<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\ClassModifier;

use PhpParser\Node\Arg;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use RuntimeException;
use SprykerSdk\Integrator\Builder\Visitor\AddUseVisitor;
use SprykerSdk\Integrator\Builder\Visitor\MethodBodyExtendVisitor;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;

class WireGlueRelationshipModifier extends AbstractGlueRelationshipModifier implements WireGlueRelationshipModifierInterface
{
    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $targetMethodName
     * @param string $key
     * @param string $classNameToAdd
     *
     * @throws \RuntimeException
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function wireGlueRelationship(
        ClassInformationTransfer $classInformationTransfer,
        string $targetMethodName,
        string $key,
        string $classNameToAdd
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

        if ($this->isRelationshipExists($methodNode, $key, $classNameToAdd)) {
            return $classInformationTransfer;
        }

        [$keyClass, $keyConst] = explode('::', $key);

        $this->nodeTraverser->addVisitor(new AddUseVisitor($classNameToAdd));
        $this->nodeTraverser->addVisitor(new AddUseVisitor($keyClass));

        $methodBody = $this->getMethodBody($methodNode, $classNameToAdd, $keyClass, $keyConst);

        $this->nodeTraverser->addVisitor(new MethodBodyExtendVisitor($targetMethodName, $methodBody));
        $classInformationTransfer->setClassTokenTree($this->nodeTraverser->traverse($classInformationTransfer->getClassTokenTree()));

        return $classInformationTransfer;
    }

    /**
     * @param \PhpParser\Node\Stmt\ClassMethod $methodNode
     * @param string $classNameToAdd
     * @param string $keyClass
     * @param string $keyConst
     *
     * @return array<\PhpParser\Node\Stmt\Expression>
     */
    protected function getMethodBody(ClassMethod $methodNode, string $classNameToAdd, string $keyClass, string $keyConst): array
    {
        $arguments = [
            new Arg($this->builderFactory->classConstFetch($this->classHelper->getShortClassName($keyClass), $keyConst)),
            new Arg($this->builderFactory->new($this->classHelper->getShortClassName($classNameToAdd))),
        ];

        return [
            new Expression(
                $this->builderFactory->methodCall(
                    $methodNode->params[0]->var,
                    'addRelationship',
                    $this->builderFactory->args($arguments),
                ),
            ),
        ];
    }
}
