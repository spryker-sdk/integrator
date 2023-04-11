<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Transfer;

use Exception;

class ModuleFilterTransfer
{
    /**
     * @var string
     */
    public const MODULE = 'module';

    /**
     * @var string
     */
    public const ORGANIZATION = 'organization';

    /**
     * @var string
     */
    protected $module;

    /**
     * @var string
     */
    protected $organization;

    /**
     * @param string $module
     *
     * @return $this
     */
    public function setModule(string $module)
    {
        $this->module = $module;

        return $this;
    }

    /**
     * @return string
     */
    public function getModule(): string
    {
        return $this->module;
    }

    /**
     * @return string
     */
    public function getModuleOrFail(): string
    {
        if ($this->module === null) {
            $this->throwNullValueException(static::MODULE);
        }

        return (string)$this->module;
    }

    /**
     * @param string $organization
     *
     * @return $this
     */
    public function setOrganization(string $organization)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrganization(): string
    {
        return $this->organization;
    }

    /**
     * @return string
     */
    public function getOrganizationOrFail(): string
    {
        if ($this->organization === null) {
            $this->throwNullValueException(static::ORGANIZATION);
        }

        return (string)$this->organization;
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
