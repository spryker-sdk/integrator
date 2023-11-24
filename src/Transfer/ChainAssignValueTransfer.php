<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Transfer;

use PhpParser\Node\Expr;

class ChainAssignValueTransfer
{
    /**
     * @var array<string>
     */
    protected array $keys = [];

    /**
     * @var \PhpParser\Node\Expr|null;
     */
    protected ?Expr $value = null;

    /**
     * @return array<string>
     */
    public function getKeys(): array
    {
        return $this->keys;
    }

    /**
     * @param string $key
     *
     * @return void
     */
    public function addKey(string $key): void
    {
        $this->keys[] = $key;
    }

    /**
     * @return \PhpParser\Node\Expr|null
     */
    public function getValue(): ?Expr
    {
        return $this->value;
    }

    /**
     * @param \PhpParser\Node\Expr $value
     *
     * @return void
     */
    public function setValue(Expr $value): void
    {
        $this->value = $value;
    }
}
