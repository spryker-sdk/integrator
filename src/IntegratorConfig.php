<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator;

class IntegratorConfig
{
    /**
     * @var string
     */
    public const MANIFEST_KEY_TARGET = 'target';

    /**
     * @var string
     */
    public const MANIFEST_KEY_SOURCE = 'source';

    /**
     * @var string
     */
    public const MANIFEST_KEY_VALUE = 'value';

    /**
     * @var string
     */
    public const MANIFEST_KEY_PREVIOUS_VALUE = 'previousValue';

    /**
     * @var string
     */
    public const MANIFEST_KEY_DEFAULT_VALUE = 'default';

    /**
     * @var string
     */
    public const MANIFEST_KEY_IS_LITERAL = 'is_literal';

    /**
     * @var string
     */
    public const MANIFEST_KEY_CHOICES = 'choices';

    /**
     * @var string
     */
    public const MANIFEST_KEY_POSITION = 'position';

    /**
     * @var string
     */
    public const MANIFEST_KEY_POSITION_BEFORE = 'before';

    /**
     * @var string
     */
    public const MANIFEST_KEY_POSITION_AFTER = 'after';

    /**
     * @var string
     */
    public const CORE_NAMESPACES = 'CORE_NAMESPACES';

    /**
     * @var string
     */
    public const PROJECT_NAMESPACES = 'PROJECT_NAMESPACES';

    /**
     * @var string
     */
    protected const MANIFESTS_DIRECTORY = 'vendor/spryker-sdk/integrator/data/manifests/';

    /**
     * @var string
     */
    protected const MANIFESTS_URL = 'https://github.com/spryker-sdk/integrator-manifests/archive/master.zip';

    /**
     * @var array<string, mixed>|null
     */
    protected $config;

    /**
     * @var array<string>
     */
    protected $organizationPathFragments = [
        'spryker',
        'spryker-shop',
        'spryker-eco',
        'spryker-sdk',
        'spryker-merchant-portal',
    ];

    /**
     * @var static|null
     */
    protected static $instance;

    final protected function __construct()
    {
    }

    /**
     * @api
     *
     * @return static
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
            static::$instance->loadConfig();
        }

        return static::$instance;
    }

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
     * @return void
     */
    protected function prepareSharedConfigDependencies(): void
    {
        defined('APPLICATION_STORE') || define('APPLICATION_STORE', 'DE');
    }

    /**
     * @return array
     */
    protected function loadSharedConfig(): array
    {
        $this->prepareSharedConfigDependencies();

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
     * @return array<string>
     */
    public function getProjectNamespaces(): array
    {
        /** @var array<string, mixed> $config */
        $config = $this->config;

        return $config[static::PROJECT_NAMESPACES];
    }

    /**
     * @return array<string>
     */
    public function getCoreNamespaces(): array
    {
        /** @var array<string, mixed> $config */
        $config = $this->config;

        return $config[static::CORE_NAMESPACES];
    }

    /**
     * @return array<string>
     */
    public function getCoreNonSplitOrganisations(): array
    {
        return [
            'Spryker',
            'SprykerShop',
            'SprykerMerchantPortal',
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
    public function getVendorDirectory(): string
    {
        return APPLICATION_VENDOR_DIR . DIRECTORY_SEPARATOR;
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
    public function getApplicationSourceDir(): string
    {
        return APPLICATION_SOURCE_DIR;
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
    public function getManifestsDirectory(): string
    {
        return $this->getProjectRootDirectory() . static::MANIFESTS_DIRECTORY;
    }

    /**
     * @return string
     */
    public function getManifestsRepository(): string
    {
        if (defined('TEST_INTEGRATOR_MODE')) {
            return 'tests/_data/manifests/archive.zip';
        }

        return static::MANIFESTS_URL;
    }

    /**
     * @return array
     */
    public function getApplications(): array
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
     * @return array<string>
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
     * @return array<string>
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
