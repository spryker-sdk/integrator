<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Common\UtilText;

use Laminas\Filter\FilterChain;
use Laminas\Filter\StringToLower;
use Laminas\Filter\Word\CamelCaseToDash;
use Laminas\Filter\Word\DashToCamelCase;
use SprykerSdk\Integrator\Common\UtilText\Filter\CamelCaseToDash as CamelCaseToDashWithoutAbbreviation;

class TextCaseHelper
{
    /**
     * @param string $value
     * @param bool $separateAbbreviation
     *
     * @return string
     */
    public static function camelCaseToDash(string $value, bool $separateAbbreviation = true): string
    {
        $filterChain = new FilterChain();

        if ($separateAbbreviation) {
            $filterChain->attach(new CamelCaseToDash());
        } else {
            $filterChain->attach(new CamelCaseToDashWithoutAbbreviation());
        }

        $filterChain->attach(new StringToLower());

        return $filterChain->filter($value);
    }

    /**
     * @param string $value
     * @param bool $upperCaseFirst
     *
     * @return string
     */
    public static function dashToCamelCase(string $value, bool $upperCaseFirst = true): string
    {
        $filterChain = new FilterChain();
        $filterChain->attach(new DashToCamelCase());

        if ($upperCaseFirst) {
            return ucfirst($filterChain->filter($value));
        }

        // Set first character in original case

        return mb_substr($value, 0, 1) . mb_substr($filterChain->filter($value), 1);
    }
}
