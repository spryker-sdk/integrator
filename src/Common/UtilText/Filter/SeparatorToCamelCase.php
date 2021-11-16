<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Common\UtilText\Filter;

class SeparatorToCamelCase
{
    /**
     * @param string $string
     * @param string $separator
     * @param bool $upperCaseFirst
     *
     * @return string
     */
    public function filter(string $string, string $separator = '-', bool $upperCaseFirst = false): string
    {
        if ($separator === '') {
            return '';
        }

        // This should be the fastest solution compared to
        // any preg_*() or array_map() solution
        $explodedString = explode($separator, $string);
        if (count($explodedString) == 0) {
            return '';
        }

        $result = $upperCaseFirst ? '' : array_shift($explodedString);

        foreach ($explodedString as $part) {
            $result .= ucfirst($part);
        }

        return $result;
    }
}
