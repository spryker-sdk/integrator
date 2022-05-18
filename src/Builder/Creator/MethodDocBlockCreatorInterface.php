<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Creator;

use PhpParser\Comment\Doc;

interface MethodDocBlockCreatorInterface
{
    /**
     * @param mixed $value
     *
     * @return \PhpParser\Comment\Doc
     */
    public function createMethodDocBlock($value): Doc;
}
