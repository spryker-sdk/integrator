<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Common\UtilText\Filter;

class CamelCaseToSeparator
{
    /**
     * @param string $string
     * @param string $separator
     *
     * @return string
     */
    public function filter(string $string, string $separator = '-'): string
    {
        $filtered = (string)preg_replace('/([a-z])([A-Z])/', '$1' . addcslashes($separator, '$') . '$2', $string);

        return strtolower($filtered);
    }
}
