<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Communication\ReleaseApp;

use JsonSerializable;

/**
 * @codeCoverageIgnore
 */
class ModuleRatingRequestDto implements JsonSerializable
{
    /**
     * @var string
     */
    protected string $organizationName;

    /**
     * @var string
     */
    protected string $moduleName;

    /**
     * @var string
     */
    protected string $moduleVersion;

    /**
     * @param string $organizationName
     * @param string $moduleName
     * @param string $moduleVersion
     */
    public function __construct(string $organizationName, string $moduleName, string $moduleVersion)
    {
        $this->organizationName = $organizationName;
        $this->moduleName = $moduleName;
        $this->moduleVersion = $moduleVersion;
    }

    /**
     * @return string
     */
    public function getOrganizationName(): string
    {
        return $this->organizationName;
    }

    /**
     * @return string
     */
    public function getModuleName(): string
    {
        return $this->moduleName;
    }

    /**
     * @return string
     */
    public function getModuleVersion(): string
    {
        return $this->moduleVersion;
    }

    /**
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return [
            'organization' => $this->organizationName,
            'name' => $this->moduleName,
            'version' => $this->moduleVersion,
        ];
    }
}
