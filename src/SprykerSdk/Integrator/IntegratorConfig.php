<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator;

use SprykerSdk\Integrator\Common\AbstractConfig;

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
     * @var string[]
     */
    protected $organizationPathFragments = [
        'spryker',
        'spryker-shop',
        'spryker-eco',
        'spryker-sdk',
        'spryker-merchant-portal',
    ];

    /**
     * @return void
     */
    public function loadConfig(): void
    {
        if ($this->config !== null) {
            return;
        }

        $this->config = array_merge($this->loadSharedConfig(), $this->loadIntegratorConfig());
    }

    /**
     * @return array
     */
    protected function loadSharedConfig(): array
    {
        $fileName = $this->getSharedConfigPath();

        if (!file_exists($fileName)) {
            return [];
        }

        include $fileName;

        return ${$this->getConfigVariableName()};

    }

    /**
     * @return array
     */
    protected function loadIntegratorConfig(): array
    {
        $fileName = $this->getDefaultConfigPath();

        if (!file_exists($fileName)) {
            return [];
        }

        include $fileName;

        return ${$this->getConfigVariableName()};
    }

    /**
     * @return string[]
     */
    public function getProjectNamespaces(): array
    {
        return $this->config[static::PROJECT_NAMESPACES];
    }

    /**
     * @return string[]
     */
    public function getCoreNamespaces(): array
    {
        return $this->config[static::CORE_NAMESPACES];
    }

    /**
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
     * @return string
     */
    public function getProjectRootDirectory(): string
    {
        return APPLICATION_ROOT_DIR . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    public function getCoreRootDirectory(): string
    {
        return APPLICATION_ROOT_DIR . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR;
    }

    /**
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
     * @return string
     */
    public function getConfigVariableName(): string
    {
        return 'config';
    }

    /**
     * @return string
     */
    public function getConfigPath(): string
    {
        return $this->getSharedConfigPath();
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
        return $this->getProjectRootDirectory() . 'config_integrator.php';
    }

    /**
     * @return string
     * @api
     *
     */
    public function getIntegratorLockFilePath(): string
    {
        return $this->getProjectRootDirectory() . 'integrator.lock';
    }

    /**
     * @return string
     */
    public function getComposerLockFilePath(): string
    {
        return $this->getProjectRootDirectory() . 'composer.lock';
    }

    /**
     * @return string
     */
    public function getRecipesDirectory(): string
    {
        return $this->getProjectRootDirectory() . 'vendor/spryker-sdk/integrator/data/recipies/';
    }

    /**
     * @return string
     */
    public function getRecipesRepository(): string
    {
        if (defined('TEST_INTEGRATOR_MODE')) {
            return 'tests/_data/recipes/archive.zip';
        }

        return 'https://github.com/spryker-sdk/integrator-recipes/archive/master.zip';
    }

    /**
     * @return string[]
     * @api
     *
     */
    public function getInternalOrganizations(): array
    {
        return [
            'Spryker',
            'SprykerShop',
            'SprykerMerchantPortal',
        ];
    }

    /**
     * @return array
     * @api
     *
     */
    public function getApplications()
    {
        return [
            'Client',
            'Service',
            'Shared',
            'Yves',
            'Zed',
            'Glue',
        ];
    }

    /**
     * @return string[]
     * @api
     *
     */
    public function getInternalPackagePathFragments(): array
    {
        return [
            'spryker',
            'spryker-shop',
            'spryker-merchant-portal',
        ];
    }

    /**
     * @return string[]
     * @api
     *
     */
    public function getPathsToInternalOrganizations(): array
    {
        $organizationPaths = [];
        foreach ($this->organizationPathFragments as $organizationPathFragment) {
            $nonsplitDirectory = sprintf('%s/vendor/spryker/%s/Bundles/', APPLICATION_ROOT_DIR, $organizationPathFragment);
            if (is_dir($nonsplitDirectory)) {
                $organizationPaths[] = $nonsplitDirectory;

                continue;
            }

            $splitDirectory = sprintf('%s/vendor/%s/', APPLICATION_ROOT_DIR, $organizationPathFragment);
            if (is_dir($splitDirectory)) {
                $organizationPaths[] = $splitDirectory;
            }
        }

        return $organizationPaths;
    }
}
