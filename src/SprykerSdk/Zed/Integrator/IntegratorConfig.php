<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerSdk\Zed\Integrator;

use Spryker\Shared\Config\Config;
use Spryker\Shared\Kernel\KernelConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class IntegratorConfig extends AbstractBundleConfig
{
    public const MANIFEST_KEY_TARGET = 'target';
    public const MANIFEST_KEY_SOURCE = 'source';
    public const MANIFEST_KEY_VALUE = 'value';
    public const MANIFEST_KEY_DEFAULT_VALUE = 'default';
    public const MANIFEST_KEY_CHOICES = 'choices';
    public const MANIFEST_KEY_POSITION = 'position';
    public const MANIFEST_KEY_POSITION_BEFORE = 'before';
    public const MANIFEST_KEY_POSITION_AFTER = 'after';

    /**
     * @api
     *
     * @return string[]
     */
    public function getProjectNamespaces(): array
    {
        return Config::get(KernelConstants::PROJECT_NAMESPACES);
    }

    /**
     * @api
     *
     * @return string[]
     */
    public function getCoreNamespaces(): array
    {
        return Config::get(KernelConstants::CORE_NAMESPACES);
    }

    /**
     * @api
     *
     * @internal
     *
     * @return string[]
     */
    public function getCoreNonSplitOrganisations(): array
    {
        return [
            'Spryker',
            'SprykerShop',
        ];
    }

    /**
     * @api
     *
     * @return string
     */
    public function getProjectRootDirectory(): string
    {
        return APPLICATION_ROOT_DIR . DIRECTORY_SEPARATOR;
    }

    /**
     * @api
     *
     * @return string
     */
    public function getCoreRootDirectory(): string
    {
        return APPLICATION_ROOT_DIR . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR;
    }

    /**
     * @api
     *
     * @internal
     *
     * @return string
     */
    public function getNonSplitRepositoryPathPattern(): string
    {
        return 'spryker'
            . DIRECTORY_SEPARATOR
            . '%s'
            . DIRECTORY_SEPARATOR
            . 'Bundles'
            . DIRECTORY_SEPARATOR;
    }

    /**
     * @api
     *
     * @return string
     */
    public function getSharedConfigVariableName(): string
    {
        return '$config';
    }

    /**
     * @api
     *
     * @return string
     */
    public function getSharedConfigPath(): string
    {
        return $this->getProjectRootDirectory()
            . 'config'
            . DIRECTORY_SEPARATOR
            . 'Shared'
            . DIRECTORY_SEPARATOR
            . 'config_default.php';
    }

    /**
     * @api
     *
     * @return string
     */
    public function getIntegratorLockFilePath(): string
    {
        return $this->getProjectRootDirectory() . 'integrator.lock';
    }

    /**
     * @api
     *
     * @return string
     */
    public function getComposerLockFilePath(): string
    {
        return $this->getProjectRootDirectory() . 'composer.lock';
    }

    /**
     * @api
     *
     * @return string
     */
    public function getRecipiesDirectory(): string
    {
        return $this->getProjectRootDirectory() . 'vendor/spryker-sdk/integrator/data/recipies/';
    }

    /**
     * @api
     *
     * @return string
     */
    public function getRecipiesRepository(): string
    {
        return 'https://github.com/spryker-sdk/integrator-recipes/archive/master.zip';
    }
}
