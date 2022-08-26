<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Applicable;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;

class ApplicableReturnClassModifierStrategy implements ApplicableInterface
{
    /**
     * @var array<string>
     */
    protected const AVAILABLE_NODE_SUFFIXES = ['plugin', 'subscriber', 'widget'];

    /**
     * @var array<string>
     */
    protected const FORBIDDEN_NODE_SUFFIXES = ['\container', 'collection'];

    /**
     * @param \PhpParser\Node\Stmt\ClassMethod $node
     *
     * @return bool
     */
    public function isApplicable(ClassMethod $node): bool
    {
        if (
            !$node->getReturnType() instanceof Node
        ) {
            return false;
        }

        $returnType = $node->getReturnType()->toString();

        foreach (static::FORBIDDEN_NODE_SUFFIXES as $pattern) {
            if (strpos(strtolower($returnType), $pattern)) {
                return false;
            }
        }

        foreach (static::AVAILABLE_NODE_SUFFIXES as $pattern) {
            if (strpos(strtolower($returnType), $pattern)) {
                return true;
            }
        }

        return false;
    }
}
