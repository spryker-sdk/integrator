<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator;

use PhpParser\ParserFactory;
use RuntimeException;
use SprykerSdk\Integrator\ConfigReader\ConfigReader;

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
    public const MANIFEST_KEY_INDEX = 'index';

    /**
     * @var string
     */
    public const MANIFEST_KEY_CONDITION = 'condition';

    /**
     * @var string
     */
    public const MANIFEST_KEY_IS_LITERAL = 'is_literal';

    /**
     * @var string
     */
    public const MANIFEST_KEY_PREVIOUS_VALUE = 'previousValue';

    /**
     * @var string
     */
    public const MANIFEST_KEY_DEFAULT_VALUE = 'defaultValue';

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
    public const MANIFEST_KEY_ARGUMENTS = 'arguments';

    /**
     * @var string
     */
    public const MANIFEST_KEY_ARGUMENTS_PREPEND = 'prepend-arguments';

    /**
     * @var string
     */
    public const MANIFEST_KEY_ARGUMENTS_APPEND = 'append-arguments';

    /**
     * @var string
     */
    public const MANIFEST_KEY_ARGUMENTS_CONSTRUCTOR = 'constructor-arguments';

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
    public const MANIFEST_KEY_CALL = 'call';

    /**
     * @var string
     */
    public const CORE_NAMESPACES_CONFIG_KEY = 'KernelConstants::CORE_NAMESPACES';

    /**
     * @var string
     */
    public const PROJECT_NAMESPACES_CONFIG_KEY = 'KernelConstants::PROJECT_NAMESPACES';

    /**
     * @var string
     */
    public const MODULE_KEY = 'module';

    /**
     * @var string
     */
    public const MODULE_VERSION_KEY = 'module-version';

    /**
     * @var string
     */
    public const INTEGRATOR_FILE_BUCKET_NAME = 'INTEGRATOR_FILE_BUCKET_NAME';

    /**
     * @var string
     */
    public const INTEGRATOR_FILE_BUCKET_CREDENTIALS_KEY = 'INTEGRATOR_FILE_BUCKET_CREDENTIALS_KEY';

    /**
     * @var string
     */
    public const INTEGRATOR_FILE_BUCKET_CREDENTIALS_SECRET = 'INTEGRATOR_FILE_BUCKET_CREDENTIALS_SECRET';

    /**
     * @var string
     */
    public const INTEGRATOR_FILE_BUCKET_REGION = 'INTEGRATOR_FILE_BUCKET_REGION';

    /**
     * @var string
     */
    protected const MANIFESTS_DIRECTORY = 'vendor/spryker-sdk/integrator/data/manifests/';

    /**
     * @var string
     */
    protected const MANIFESTS_URL = 'https://github.com/spryker-sdk/integrator-manifests/archive/master.zip';

    /**
     * @var string
     */
    protected const LOCAL_RECIPES_DIRECTORY = 'vendor/spryker-sdk/integrator-recipes/';

    /**
     * @var string
     */
    protected const PATH_GLOSSARY = 'data/import/common/common/glossary.csv';

    /**
     * @var string
     */
    protected const PHPCS_XML_FILE_NAME = 'phpcs.xml';

    /**
     * @var array<string, mixed>|null
     */
    protected $config;

    /**
     * @var static|null
     */
    protected static $instance;

    /**
     * @var \SprykerSdk\Integrator\ConfigReader\ConfigReaderInterface
     */
    protected $configReader;

    final protected function __construct()
    {
        $this->configReader = new ConfigReader(new ParserFactory());
    }

    /**
     * @api
     *
     * @return static
     */
    public static function getInstance()
    {
        if (
            static::$instance === null ||
            (defined('TEST_INTEGRATOR_MODE') && TEST_INTEGRATOR_MODE === 'true')
        ) {
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

        $this->config = array_merge(
            $this->readConfigFile($this->getSharedConfigPath()),
            $this->readConfigFile($this->getDefaultConfigPath()),
        );
    }

    /**
     * @param string $fileName
     *
     * @return array<string, mixed>
     */
    protected function readConfigFile(string $fileName): array
    {
        if (!file_exists($fileName)) {
            return [];
        }

        return $this->readConfig($fileName, $this->getConfigKeys());
    }

    /**
     * @param string $configPath
     * @param array $configKeys
     *
     * @throws \RuntimeException
     *
     * @return array
     */
    protected function readConfig(string $configPath, array $configKeys): array
    {
        $configValues = $this->configReader->read($configPath, $configKeys);

        $missedConfigKeys = array_diff($configKeys, array_keys($configValues));

        if ($missedConfigKeys) {
            throw new RuntimeException(sprintf('Unable to read config keys %s', implode(', ', $missedConfigKeys)));
        }

        return $configValues;
    }

    /**
     * @return array<string>
     */
    protected function getConfigKeys(): array
    {
        return [static::CORE_NAMESPACES_CONFIG_KEY, static::PROJECT_NAMESPACES_CONFIG_KEY];
    }

    /**
     * @return array<string>
     */
    public function getProjectNamespaces(): array
    {
        /** @var array<string, mixed> $config */
        $config = $this->config;

        return $config[static::PROJECT_NAMESPACES_CONFIG_KEY];
    }

    /**
     * @return array<string>
     */
    public function getCoreNamespaces(): array
    {
        /** @var array<string, mixed> $config */
        $config = $this->config;

        return $config[static::CORE_NAMESPACES_CONFIG_KEY];
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
     * This is used for local development purposes only.
     *
     * @return string
     */
    public function getLocalRecipesDirectory(): string
    {
        return $this->getProjectRootDirectory() . static::LOCAL_RECIPES_DIRECTORY;
    }

    /**
     * @return string
     */
    public function getGlossaryFilePath(): string
    {
        return $this->getProjectRootDirectory() . static::PATH_GLOSSARY;
    }

    /**
     * @return string
     */
    public function getManifestsRepository(): string
    {
        if (defined('TEST_INTEGRATOR_MODE')) {
            return 'tests/_data/archive.zip';
        }

        return static::MANIFESTS_URL;
    }

    /**
     * @return string
     */
    public function getPhpCsConfigPath(): string
    {
        return $this->getProjectRootDirectory() . static::PHPCS_XML_FILE_NAME;
    }

    /**
     * @return string
     */
    public function getFileBucketName(): string
    {
        return (string)getenv(static::INTEGRATOR_FILE_BUCKET_NAME);
    }

    /**
     * @return string
     */
    public function getFileBucketCredentialsKey(): string
    {
        return (string)getenv(static::INTEGRATOR_FILE_BUCKET_CREDENTIALS_KEY);
    }

    /**
     * @return string
     */
    public function getFileBucketCredentialsSecret(): string
    {
        return (string)getenv(static::INTEGRATOR_FILE_BUCKET_CREDENTIALS_SECRET);
    }

    /**
     * @return string
     */
    public function getFileBucketRegion(): string
    {
        return (string)getenv(static::INTEGRATOR_FILE_BUCKET_REGION);
    }
}
