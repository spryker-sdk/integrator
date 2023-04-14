<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Transfer;

use Exception;

class IntegratorCommandArgumentsTransfer
{
    /**
     * @var string
     */
    public const SOURCE = 'source';

    /**
     * @var string
     */
    public const FORMAT = 'format';

    /**
     * @var string
     */
    public const IS_DRY = 'isDry';

    /**
     * @var string
     */
    public const RELEASE_GROUP_ID = 'releaseGroupId';

    /**
     * @var string
     */
    public const BRANCH_TO_COMPARE = 'branchToCompare';

    /**
     * @var string
     */
    public const MODULES = 'modules';

    /**
     * @var string|null
     */
    protected ?string $source = null;

    /**
     * @var string|null
     */
    protected ?string $format = null;

    /**
     * @var bool
     */
    protected bool $isDry = false;

    /**
     * @var int|null
     */
    protected ?int $releaseGroupId = null;

    /**
     * @var string|null
     */
    protected ?string $branchToCompare = null;

    /**
     * @var array<\SprykerSdk\Integrator\Transfer\ModuleTransfer>
     */
    protected array $modules = [];

    /**
     * @param string|null $source
     *
     * @return $this
     */
    public function setSource(?string $source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSource(): ?string
    {
        return $this->source;
    }

    /**
     * @return string
     */
    public function getSourceOrFail(): string
    {
        if ($this->source === null) {
            $this->throwNullValueException(static::SOURCE);
        }

        return (string)$this->source;
    }

    /**
     * @param string|null $format
     *
     * @return $this
     */
    public function setFormat(?string $format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFormat(): ?string
    {
        return $this->format;
    }

    /**
     * @return string
     */
    public function getFormatOrFail(): string
    {
        if ($this->format === null) {
            $this->throwNullValueException(static::FORMAT);
        }

        return (string)$this->format;
    }

    /**
     * @param bool $isDry
     *
     * @return $this
     */
    public function setIsDry(bool $isDry)
    {
        $this->isDry = $isDry;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsDry(): ?bool
    {
        return $this->isDry;
    }

    /**
     * @return bool
     */
    public function getIsDryOrFail(): bool
    {
        return $this->isDry;
    }

    /**
     * @return int|null
     */
    public function getReleaseGroupId(): ?int
    {
        return $this->releaseGroupId;
    }

    /**
     * @param int|null $releaseGroupId
     *
     * @return void
     */
    public function setReleaseGroupId(?int $releaseGroupId): void
    {
        $this->releaseGroupId = $releaseGroupId;
    }

    /**
     * @return int
     */
    public function getReleaseGroupIdOrFail(): int
    {
        if ($this->releaseGroupId === null) {
            $this->throwNullValueException(static::RELEASE_GROUP_ID);
        }

        return (int)$this->releaseGroupId;
    }

    /**
     * @return string|null
     */
    public function getBranchToCompare(): ?string
    {
        return $this->branchToCompare;
    }

    /**
     * @param string|null $branchToCompare
     *
     * @return void
     */
    public function setBranchToCompare(?string $branchToCompare): void
    {
        $this->branchToCompare = $branchToCompare;
    }

    /**
     * @return string
     */
    public function getBranchToCompareOrFail(): string
    {
        if ($this->branchToCompare === null) {
            $this->throwNullValueException(static::BRANCH_TO_COMPARE);
        }

        return (string)$this->branchToCompare;
    }

    /**
     * @return array<\SprykerSdk\Integrator\Transfer\ModuleTransfer>
     */
    public function getModules(): array
    {
        return $this->modules;
    }

    /**
     * @param array<\SprykerSdk\Integrator\Transfer\ModuleTransfer> $modules
     *
     * @return void
     */
    public function setModules(array $modules): void
    {
        $this->modules = $modules;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ModuleTransfer $module
     *
     * @return void
     */
    public function addModule(ModuleTransfer $module): void
    {
        $this->modules[] = $module;
    }

    /**
     * @param string $propertyName
     *
     * @throws \Exception
     *
     * @return void
     */
    protected function throwNullValueException(string $propertyName): void
    {
        throw new Exception(
            sprintf('Property "%s" of transfer `%s` is null.', $propertyName, static::class),
        );
    }
}
