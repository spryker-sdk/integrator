<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\ManifestStrategy;

use DOMDocument;
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
            $navigation = $this->getNavigation();
        } catch (UnexpectedNavigationXmlStructureException $unexpectedNavigationXmlStructureException) {
            return false;
        }

        $navigation = $this->applyNewNavigation(
            $navigation,
            $manifest[static::KEY_NAVIGATIONS_CONFIGURATION],
            $manifest[static::KEY_NAVIGATION_POSITION_BEFORE] ?? null,
            $manifest[static::KEY_NAVIGATION_POSITION_AFTER] ?? null,
        );

        if ($isDry) {
            $inputOutput->writeln($this->getXmlNavigationFromArrayNavigation($navigation)->asXML() ?: '');

            return true;
        }

        return $this->writeNavigationSchema($navigation);
    }

    /**
     * @return array<string|int, array|string>
     */
    protected function getNavigation(): array
    {
        $mainNavigationXmlElement = simplexml_load_file($this->getNavigationSourceFilePath());
        $mainNavigationAsJson = json_encode($mainNavigationXmlElement);

        if ($mainNavigationAsJson === false) {
            throw new UnexpectedNavigationXmlStructureException();
        }

        return json_decode($mainNavigationAsJson, true);
    }

    /**
     * @param array<string|int, array|string> $navigation
     * @param array<string|int, array|string> $newNavigations
     * @param string|null $before
     * @param string|null $after
     *
     * @return array<string|int, array|string>
     */
    protected function applyNewNavigation(
        array $navigation,
        array $newNavigations,
        ?string $before = null,
        ?string $after = null
    ): array {
        $key = $before ?? $after;
        $position = array_search($key, array_keys($navigation));

        if ($position === false) {
            return $this->addNewNavigations($navigation, $newNavigations);
        }

        $offset = $position + 1;

        if ($before !== null) {
            $offset--;
        }

        return array_slice($navigation, 0, $offset, true)
            + $newNavigations
            + array_slice($navigation, $offset, null, true);
    }

    /**
     * @param array<string|int, array|string> $navigation
     * @param array<string|int, array|string> $newNavigations
     *
     * @return array<string|int, array|string>
     */
    protected function addNewNavigations(
        array $navigation,
        array $newNavigations
    ): array {
        foreach ($newNavigations as $navigationItemKey => $navigationItemData) {
            if (isset($navigation[$navigationItemKey])) {
                continue;
            }

            $navigation[$navigationItemKey] = $navigationItemData;
        }

        return $navigation;
    }

    /**
     * @param array<string|int, array|string> $navigation
     *
     * @return bool
     */
    protected function writeNavigationSchema(array $navigation): bool
    {
        $navigationXmlElement = $this->getXmlNavigationFromArrayNavigation($navigation);
        $xmlString = $navigationXmlElement->asXML();

        if ($xmlString === false) {
            return false;
        }

        $navigationXmlDomDocument = new DOMDocument();
        $navigationXmlDomDocument->preserveWhiteSpace = false;
        $navigationXmlDomDocument->formatOutput = true;
        $navigationXmlDomDocument->loadXML($xmlString);

        return $navigationXmlDomDocument->save($this->getNavigationSourceFilePath()) !== false;
    }

    /**
     * @param array<string|int, array|string> $navigation
     *
     * @return \SimpleXMLElement
     */
    protected function getXmlNavigationFromArrayNavigation(array $navigation): SimpleXMLElement
    {
        return $this->transformNavigationToXmlElement(
            $navigation,
            new SimpleXMLElement('<config/>')
        );
    }

    /**
     * @param array<string|int, array|string> $navigation
     * @param SimpleXMLElement $parentXmlElement
     *
     * @return SimpleXMLElement
     */
    public function transformNavigationToXmlElement(array $navigation, SimpleXMLElement $parentXmlElement): SimpleXMLElement
    {
        foreach ($navigation as $navigationName => $navigationData) {
            if (is_int($navigationName) || (is_array($navigationData) && empty($navigationData))) {
                continue;
            }

            if (is_array($navigationData)) {
                $childElement = $parentXmlElement->addChild($navigationName);
                $this->transformNavigationToXmlElement($navigationData, $childElement);

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
