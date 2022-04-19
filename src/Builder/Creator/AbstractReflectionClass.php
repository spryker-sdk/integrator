<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Creator;

class AbstractReflectionClass
{
    /**
     * @var string
     */
    protected const ARRAY_MERGE_FUNCTION = 'array_merge';

    /**
     * @var string
     */
    protected const RETURN_TYPE_STRING = 'string';

    /**
     * @var string
     */
    protected const RETURN_TYPE_ARRAY = 'array';

    /**
     * @var string
     */
    protected const RETURN_TYPE_BOOL = 'bool';

    /**
     * @param mixed $value
     *
     * @return bool
     */
    protected function isValueReturnArray($value): bool
    {
        return is_array($value) || preg_match('/' . static::ARRAY_MERGE_FUNCTION . '/', $value);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    protected function isValueReturnBool($value): bool
    {
        return in_array($value, ['true', 'false']);
    }
}
