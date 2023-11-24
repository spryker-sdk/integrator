<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Transfer;

class ExtractedValueTransfer
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var bool
     */
    protected bool $source = false;

    /**
     * @var bool
     */
    protected bool $isLiteral = false;

    /**
     * @param mixed $value
     * @param bool $isLiteral
     * @param bool $source
     */
    public function __construct($value, bool $isLiteral = false, bool $source = false)
    {
        $this->value = $value;
        $this->isLiteral = $isLiteral;
        $this->source = $source;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isLiteral(): bool
    {
        return $this->isLiteral;
    }

    /**
     * @return bool
     */
    public function isSource(): bool
    {
        return $this->source;
    }
}
