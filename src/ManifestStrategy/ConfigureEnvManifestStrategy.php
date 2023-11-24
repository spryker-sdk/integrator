<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\ManifestStrategy;

use SprykerSdk\Integrator\Builder\ClassModifier\ConfigFile\ConfigFileModifierInterface;
use SprykerSdk\Integrator\Builder\Extractor\ExpressionExtractorInterface;
use SprykerSdk\Integrator\Builder\FileBuilderFacade;
use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\Exception\ManifestApplyingException;
use SprykerSdk\Integrator\Helper\ClassHelperInterface;
use SprykerSdk\Integrator\IntegratorConfig;
use SprykerSdk\Integrator\Transfer\FileInformationTransfer;

class ConfigureEnvManifestStrategy extends AbstractManifestStrategy
{
    /**
     * @var array<array-key, \SprykerSdk\Integrator\Builder\ConfigurationEnvironmentBuilder\ConfigurationEnvironmentStrategyInterface>
     */
    protected $configurationEnvironmentStrategies;

    /**
     * @var \SprykerSdk\Integrator\Builder\Extractor\ExpressionExtractorInterface
     */
    protected ExpressionExtractorInterface $expressionsValueExtractor;

    /**
     * @var \SprykerSdk\Integrator\Builder\ClassModifier\ConfigFile\ConfigFileModifierInterface
     */
    protected ConfigFileModifierInterface $configFileModifier;

    /**
     * @var \SprykerSdk\Integrator\Builder\FileBuilderFacade
     */
    protected FileBuilderFacade $fileBuilderFacade;

    /**
     * @param \SprykerSdk\Integrator\IntegratorConfig $config
     * @param \SprykerSdk\Integrator\Helper\ClassHelperInterface $classHelper
     * @param \SprykerSdk\Integrator\Builder\Extractor\ExpressionExtractorInterface $expressionsValueExtractor
     * @param \SprykerSdk\Integrator\Builder\ClassModifier\ConfigFile\ConfigFileModifierInterface $configFileModifier
     * @param \SprykerSdk\Integrator\Builder\FileBuilderFacade $fileBuilderFacade
     * @param array<array-key, \SprykerSdk\Integrator\Builder\ConfigurationEnvironmentBuilder\ConfigurationEnvironmentStrategyInterface> $configurationEnvironmentBuilders
     */
    public function __construct(
        IntegratorConfig $config,
        ClassHelperInterface $classHelper,
        ExpressionExtractorInterface $expressionsValueExtractor,
        ConfigFileModifierInterface $configFileModifier,
        FileBuilderFacade $fileBuilderFacade,
        array $configurationEnvironmentBuilders
    ) {
        parent::__construct($config, $classHelper);

        $this->fileBuilderFacade = $fileBuilderFacade;
        $this->configFileModifier = $configFileModifier;
        $this->expressionsValueExtractor = $expressionsValueExtractor;
        $this->configurationEnvironmentStrategies = $configurationEnvironmentBuilders;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'configure-env';
    }

    /**
     * @param array<mixed> $manifest
     * @param string $moduleName
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $inputOutput
     * @param bool $isDry
     *
     * @throws \SprykerSdk\Integrator\Exception\ManifestApplyingException
     *
     * @return bool
     */
    public function apply(array $manifest, string $moduleName, InputOutputInterface $inputOutput, bool $isDry): bool
    {
        $target = $manifest[IntegratorConfig::MANIFEST_KEY_TARGET];
        $value = $manifest[IntegratorConfig::MANIFEST_KEY_VALUE] ?? null;
        $choices = $manifest[IntegratorConfig::MANIFEST_KEY_CHOICES] ?? [];
        $defaultValue = $manifest[IntegratorConfig::MANIFEST_KEY_DEFAULT_VALUE] ?? null;

        if (!is_bool($value) && !$value) {
            $value = $this->askValue(
                "'Provide value for $target global configuration.'",
                $choices,
                $inputOutput,
                $defaultValue,
            );
        }

        $configFileName = $this->config->getConfigPath();
        if (!file_exists($configFileName)) {
            throw new ManifestApplyingException(sprintf(
                'File `%s` does not exist. Please check file path or customize using `IntegratorConfig::getConfigPath()`.',
                $configFileName,
            ));
        }
        if (!$isDry) {
            $this->applyValue($configFileName, $target, $value);
        }

        $inputOutput->writeln(sprintf(
            'Global config `%s` was added with to %s value `%s`',
            $target,
            $configFileName,
            is_array($value) ? json_encode($value) : $value,
        ), InputOutputInterface::DEBUG);

        return true;
    }

    /**
     * @param string $configFileName
     * @param string $target
     * @param mixed $value
     *
     * @return void
     */
    protected function applyValue(string $configFileName, string $target, mixed $value): void
    {
        $fileInformationTransfer = $this->fileBuilderFacade->loadFile($configFileName);
        $originalExpressions = $this->expressionsValueExtractor->extractExpressions($fileInformationTransfer->getOriginalTokenTree());

        if (!array_key_exists($target, $originalExpressions)) {
            file_put_contents($configFileName, $this->getConfigAppendData($target, $value), FILE_APPEND);

            return;
        }

        if (!is_array($value) || !empty($value[IntegratorConfig::MANIFEST_KEY_IS_LITERAL])) {
            return;
        }

        $this->applyDiff(
            $fileInformationTransfer,
            $target,
            $this->compareArrayExpression($value, $originalExpressions, $target),
        );

        $this->fileBuilderFacade->storeFile($fileInformationTransfer);
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\FileInformationTransfer $fileInformationTransfer
     * @param string $target
     * @param array $diffValueItems
     *
     * @return void
     */
    protected function applyDiff(FileInformationTransfer $fileInformationTransfer, string $target, array $diffValueItems): void
    {
        foreach ($diffValueItems as $item) {
            if (!is_array($item)) {
                $item = [$item];
            }

            $this->configFileModifier->addArrayItemToEnvConfig(
                $fileInformationTransfer,
                $target,
                $this->prepareValue($item),
            );
        }
    }

    /**
     * @param array $manifestValue
     * @param array $originalExpressions
     * @param string $target
     *
     * @return array
     */
    protected function compareArrayExpression(array $manifestValue, array $originalExpressions, string $target): array
    {
        $diff = [];
        foreach ($manifestValue as $key => $value) {
            if (is_int($key)) {
                if (
                    isset($originalExpressions[$target]) &&
                    is_array($originalExpressions[$target]) &&
                    !in_array($value, $originalExpressions[$target], true)
                ) {
                    $diff[] = $value;
                }

                continue;
            }

            if (isset($originalExpressions[$target]) && isset($originalExpressions[$target][$key])) {
                continue;
            }
            $diff[] = [$key => $value];
        }

        return $diff;
    }

    /**
     * @param string $target
     * @param mixed $value
     *
     * @return string
     */
    protected function getConfigAppendData(string $target, $value): string
    {
        $data = PHP_EOL . '$' . $this->config->getConfigVariableName() . '[' . $target . '] = ';

        $data .= $this->prepareValue($value);
        $data .= ';' . PHP_EOL;

        return $data;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected function prepareValue($value)
    {
        foreach ($this->configurationEnvironmentStrategies as $configurationEnvironmentStrategy) {
            if ($configurationEnvironmentStrategy->isApplicable($value)) {
                return $configurationEnvironmentStrategy->getFormattedExpression($value);
            }
        }

        return var_export($value, true);
    }
}
