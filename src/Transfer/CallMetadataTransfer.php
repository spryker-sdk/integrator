<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Transfer;

class CallMetadataTransfer extends AbstractTransfer
{
    /**
     * @var string|null
     */
    protected ?string $target = null;

    /**
     * @var string|null
     */
    protected ?string $before = null;

    /**
     * @var string|null
     */
    protected ?string $after = null;

    /**
     * @var string|null
     */
    protected ?string $index = null;

    /**
     * @return string|null
     */
    public function getTarget(): ?string
    {
        return $this->target;
    }

    /**
     * @param string|null $target
     *
     * @return void
     */
    public function setTarget(?string $target): void
    {
        $this->target = $target;
    }

    /**
     * @module App
     *
     * @return string
     */
    public function getTargetOrFail(): string
    {
        if ($this->target === null) {
            $this->throwNullValueException('TARGET');
        }

        return $this->target;
    }

    /**
     * @return string|null
     */
    public function getBefore(): ?string
    {
        return $this->before;
    }

    /**
     * @param string|null $before
     *
     * @return void
     */
    public function setBefore(?string $before): void
    {
        $this->before = $before;
    }

    /**
     * @return string|null
     */
    public function getAfter(): ?string
    {
        return $this->after;
    }

    /**
     * @param string|null $after
     *
     * @return void
     */
    public function setAfter(?string $after): void
    {
        $this->after = $after;
    }

    /**
     * @return string|null
     */
    public function getIndex(): ?string
    {
        return $this->index;
    }

    /**
     * @param string|null $index
     *
     * @return void
     */
    public function setIndex(?string $index): void
    {
        $this->index = $index;
    }
}
