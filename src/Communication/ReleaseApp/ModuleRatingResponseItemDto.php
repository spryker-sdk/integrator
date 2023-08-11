<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Communication\ReleaseApp;

class ModuleRatingResponseItemDto
{
    /**
     * @var string
     */
    protected string $name;

    /**
     * @var string
     */
    protected string $organization;

    /**
     * @var string
     */
    protected string $version;

    /**
     * @var string
     */
    protected string $rating;

    /**
     * @var int
     */
    protected int $releaseGroupId;

    /**
     * @param string $name
     * @param string $organization
     * @param string $version
     * @param string $rating
     * @param int $releaseGroupId
     */
    public function __construct(string $name, string $organization, string $version, string $rating, int $releaseGroupId)
    {
        $this->name = $name;
        $this->organization = $organization;
        $this->version = $version;
        $this->rating = $rating;
        $this->releaseGroupId = $releaseGroupId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
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
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getRating(): string
    {
        return $this->rating;
    }

    /**
     * @return int
     */
    public function getReleaseGroupId(): int
    {
        return $this->releaseGroupId;
    }
}
