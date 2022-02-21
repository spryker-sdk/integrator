<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\ManifestStrategy;

use SimpleXMLElement;
use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\Exception\UnexpectedNavigationXmlStructureException;

class WireNavigationManifestStrategy extends AbstractManifestStrategy
{
    /**
     * @var string
     */
    protected const TARGET_NAVIGATION_FILE = 'config/Zed/navigation.xml';

    /**
     * @var string
     */
    protected const KEY_NAVIGATIONS_CONFIGURATION = 'navigations';

    /**
     * @var string
     */
    protected const KEY_NAVIGATION_POSITION_AFTER = 'after';

    /**
     * @var string
     */
    protected const KEY_NAVIGATION_POSITION_BEFORE = 'before';

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'wire-navigation';
    }

    /**
     * @param array<mixed> $manifest
     * @param string $moduleName
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $inputOutput
     * @param bool $isDry
     *
     * @return bool
     */
    public function apply(array $manifest, string $moduleName, InputOutputInterface $inputOutput, bool $isDry): bool
    {
        if (!file_exists($this->getNavigationSourceFilePath())) {
            $inputOutput->writeln(sprintf(
                'The project doesn\'n have the navigation source file: %s.',
                static::TARGET_NAVIGATION_FILE,
            ), InputOutputInterface::DEBUG);

            return false;
        }

        try {
            $navigationConfiguration = $this->getNavigationConfiguration();
        } catch (UnexpectedNavigationXmlStructureException $unexpectedNavigationXmlStructureException) {
            return false;
        }
        
        $navigationConfiguration = $this->applyNewNavigationConfigurations(
            $navigationConfiguration,
            $manifest[static::KEY_NAVIGATIONS_CONFIGURATION],
            $manifest[static::KEY_NAVIGATION_POSITION_BEFORE] ?? null,
            $manifest[static::KEY_NAVIGATION_POSITION_AFTER] ?? null,
        );
        $this->saveNavigationConfigurationsIntoSourceFile($navigationConfiguration);

        return true;
    }

    /**
     * @return array<string|int, array|string>
     */
    protected function getNavigationConfiguration(): array
    {
        $mainNavigationXmlElement = simplexml_load_file($this->getNavigationSourceFilePath());
        $mainNavigationAsJson = json_encode($mainNavigationXmlElement);

        if ($mainNavigationAsJson === false) {
            throw new UnexpectedNavigationXmlStructureException();
        }

        return json_decode($mainNavigationAsJson, true);
    }

    /**
     * @param array<string|int, array|string> $navigationConfiguration
     * @param array<string|int, array|string> $newNavigationConfiguration
     * @param string|null $before
     * @param string|null $after
     *
     * @return array<string|int, array|string>
     */
    protected function applyNewNavigationConfigurations(
        array $navigationConfiguration,
        array $newNavigationConfiguration,
        ?string $before = null,
        ?string $after = null
    ): array {
        $resultNavigationConfiguration = [];
        $itemAdded = false;

        foreach ($navigationConfiguration as $navigationItemKey => $navigationItemConfiguration) {
            if ($navigationItemKey === $before) {
                $this->injectNewNavigationConfigurations($resultNavigationConfiguration, $navigationConfiguration, $newNavigationConfiguration);
                $resultNavigationConfiguration[$navigationItemKey] = $navigationItemConfiguration;
                $itemAdded = true;

                continue;
            }

            if ($navigationItemKey === $after) {
                $resultNavigationConfiguration[$navigationItemKey] = $navigationItemConfiguration;
                $this->injectNewNavigationConfigurations($resultNavigationConfiguration, $navigationConfiguration, $newNavigationConfiguration);
                $itemAdded = true;

                continue;
            }

            $resultNavigationConfiguration[$navigationItemKey] = $navigationItemConfiguration;
        }

        if (!$itemAdded) {
            $this->injectNewNavigationConfigurations($resultNavigationConfiguration, $navigationConfiguration, $newNavigationConfiguration);
        }

        return $resultNavigationConfiguration;
    }

    /**
     * @param array<string|int, array|string> $sourceNavigationConfigurations
     * @param array<string|int, array|string> $resultNavigationConfigurations
     * @param array<string|int, array|string> $newNavigationConfigurations
     *
     * @return void
     */
    protected function injectNewNavigationConfigurations(
        array &$resultNavigationConfigurations,
        array $sourceNavigationConfigurations,
        array $newNavigationConfigurations
    ): void {
        foreach ($newNavigationConfigurations as $navigationItemKey => $navigationItemConfiguration) {
            if (isset($sourceNavigationConfigurations[$navigationItemKey])) {
                continue;
            }

            $resultNavigationConfigurations[$navigationItemKey] = $navigationItemConfiguration;
        }
    }

    /**
     * @param array<string|int, array|string> $navigationConfiguration
     *
     * @return void
     */
    protected function saveNavigationConfigurationsIntoSourceFile(array $navigationConfiguration): void
    {
        $this->transformNavigationConfigurationToXmlElement(
            $navigationConfiguration,
            new SimpleXMLElement('<config/>')
        )->saveXML($this->getNavigationSourceFilePath());
    }

    /**
     * @param array<string|int, array|string> $navigationConfigurations
     * @param SimpleXMLElement $parentXmlElement
     *
     * @return SimpleXMLElement
     */
    public function transformNavigationConfigurationToXmlElement(array $navigationConfigurations, SimpleXMLElement $parentXmlElement): SimpleXMLElement
    {
        foreach ($navigationConfigurations as $navigationName => $navigationData) {
            if (is_int($navigationName) || (is_array($navigationData) && empty($navigationData))) {
                continue;
            }

            if (is_array($navigationData)) {
                $childElement = $parentXmlElement->addChild($navigationName);
                $this->transformNavigationConfigurationToXmlElement($navigationData, $childElement);

                continue;
            }

            $parentXmlElement->addChild($navigationName, (string)$navigationData);
        }

        return $parentXmlElement;
    }

    /**
     * @return string
     */
    protected function getNavigationSourceFilePath(): string
    {
        return $this->config->getProjectRootDirectory() . static::TARGET_NAVIGATION_FILE;
    }
}
