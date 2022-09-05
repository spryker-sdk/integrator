<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Common\UtilText\Filter;

use Laminas\Filter\Word\AbstractSeparator;

class CamelCaseToDash extends AbstractSeparator
{
    public function __construct()
    {
        parent::__construct('-');
    }

    /**
     * @param mixed $string
     *
     * @return string
     */
    public function filter($string): string
    {
        return (string)preg_replace('/([a-z])([A-Z])/', '$1' . addcslashes($this->getSeparator(), '$') . '$2', $string);
    }
}
