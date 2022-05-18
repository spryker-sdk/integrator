<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Checker;

use PhpParser\Node\Stmt\ClassMethod;

interface ClassMethodCheckerInterface
{
    /**
     * @param \PhpParser\Node\Stmt\ClassMethod $node
     *
     * @return bool
     */
    public function isMethodReturnArray(ClassMethod $node): bool;

    /**
     * @param \PhpParser\Node\Stmt\ClassMethod|null $methodNode
     * @param mixed $value
     *
     * @return bool
     */
    public function isMethodNodeSameAsValue(?ClassMethod $methodNode, $value): bool;
}
