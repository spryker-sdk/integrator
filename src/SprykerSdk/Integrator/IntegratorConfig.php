<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerSdk\Integrator;

use SprykerSdk\Shared\Common\AbstractConfig;

class IntegratorConfig extends AbstractConfig
{
    public const MANIFEST_KEY_TARGET = 'target';
    public const MANIFEST_KEY_SOURCE = 'source';
    public const MANIFEST_KEY_VALUE = 'value';
    public const MANIFEST_KEY_DEFAULT_VALUE = 'default';
    public const MANIFEST_KEY_CHOICES = 'choices';
    public const MANIFEST_KEY_POSITION = 'position';
    public const MANIFEST_KEY_POSITION_BEFORE = 'before';
    public const MANIFEST_KEY_POSITION_AFTER = 'after';

    public const CORE_NAMESPACES = 'CORE_NAMESPACES';
    public const PROJECT_NAMESPACES = 'PROJECT_NAMESPACES';

    /**
     * @var array|null
     */
    protected $config;

    /**
     * @return void
     */
    public function loadConfig(): void
    {
        if ($this->config === null) {
            $fileName = $this->getConfigPath();
            if (file_exists($fileName)) {
                include $fileName;
            }

            $this->config = $this->getConfigVariableName();
        }
    }

    /**
     * @api
     *
     * @return string[]
     */
    public function getProjectNamespaces(): array
    {
        return $this->config[static::PROJECT_NAMESPACES];
    }

    /**
     * @api
     *
     * @return string[]
     */
    public function getCoreNamespaces(): array
    {
        return $this->config[static::CORE_NAMESPACES];
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
    public function getConfigVariableName(): string
    {
        return '$config';
    }

    /**
     * @api
     *
     * @return string
     */
    public function getConfigPath(): string
    {
        $configFile = $this->getSharedConfigPath();

        if (!file_exists($configFile)) {
            return $this->getDefaultConfigPath();
        }

        return $configFile;
    }

    /**
     * @return string
     */
    protected function getSharedConfigPath(): string
    {
        return $this->getProjectRootDirectory()
            . 'config'
            . DIRECTORY_SEPARATOR
            . 'Shared'
            . DIRECTORY_SEPARATOR
            . 'config_default.php';
    }

    /**
     * @return string
     */
    protected function getDefaultConfigPath(): string
    {
        return $this->getProjectRootDirectory()
            . DIRECTORY_SEPARATOR
            . 'config_integrator.php';
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
    public function getRecipesDirectory(): string
    {
        return $this->getProjectRootDirectory() . 'vendor/spryker-sdk/integrator/data/recipies/';
    }

    /**
     * @api
     *
     * @return string
     */
    public function getRecipesRepository(): string
    {
        return 'https://github.com/spryker-sdk/integrator-recipes/archive/master.zip';
    }
}
