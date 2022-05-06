<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Creator;

use PhpParser\Node\Identifier;

class MethodReturnTypeCreator extends AbstractReflectionClass implements MethodReturnTypeCreatorInterface
{
    /**
     * @param mixed $value
     *
     * @return \PhpParser\Node\Identifier
     */
    public function createMethodReturnType($value): Identifier
    {
        $returnType = static::RETURN_TYPE_STRING;
        if ($this->isValueReturnArray($value)) {
            $returnType = static::RETURN_TYPE_ARRAY;
        }
        if (is_bool($value)) {
            $returnType = static::RETURN_TYPE_BOOL;
        }

        return new Identifier($returnType);
    }
}
