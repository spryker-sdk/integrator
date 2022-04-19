<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Creator;

use PhpParser\Comment\Doc;

class MethodDocBlockCreator extends AbstractReflectionClass implements MethodDocBlockCreatorInterface
{
    /**
     * @param mixed $value
     *
     * @return \PhpParser\Comment\Doc
     */
    public function createMethodDocBlock($value): Doc
    {
        $docBlockReturnItems = [];
        $docBlockReturnItems[] = '/**';
        $docBlockReturnType = static::RETURN_TYPE_STRING;
        if ($this->isValueReturnArray($value)) {
            $docBlockReturnType = static::RETURN_TYPE_ARRAY;
        }
        if ($this->isValueReturnBool($value)) {
            $docBlockReturnType = static::RETURN_TYPE_BOOL;
        }
        $docBlockReturnItems[] = ' * @return ' . $docBlockReturnType;
        $docBlockReturnItems[] = ' */';

        return new Doc(implode("\n", $docBlockReturnItems));
    }
}
