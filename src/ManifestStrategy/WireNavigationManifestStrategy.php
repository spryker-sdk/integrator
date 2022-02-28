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
     * @var string
     */
    protected const NAVIGATION_DATA_KEY_BUNDLE = 'bundle';

    /**
     * @var string
     */
    protected const NAVIGATION_DATA_KEY_MODULE = 'module';

    /**
     * @var string
     */
    protected const NAVIGATION_DATA_KEY_PAGES = 'pages';

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

        return $this->writeNavigationSchema($navigation, $inputOutput, $isDry);
    }

    /**
     * @return array<string|int, array<string, mixed>>
     */
    protected function getNavigation(): array
    {
        if (!file_exists($this->getNavigationSourceFilePath())) {
            return [];
        }

        $mainNavigationXmlElement = simplexml_load_file($this->getNavigationSourceFilePath());
        $mainNavigationAsJson = json_encode($mainNavigationXmlElement);

        if ($mainNavigationAsJson === false) {
            throw new UnexpectedNavigationXmlStructureException();
        }

        return json_decode($mainNavigationAsJson, true);
    }

    /**
     * @param array<string|int, array<string, mixed>> $navigation
     * @param array<string|int, array<string, mixed>> $newNavigations
     * @param string|null $before
     * @param string|null $after
     *
     * @return array<string|int, array<string, mixed>>
     */
    protected function applyNewNavigation(
        array $navigation,
        array $newNavigations,
        ?string $before = null,
        ?string $after = null
    ): array {
        $newNavigations = $this->prepareNewNavigationsToApplying($newNavigations);
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
     * @param array<string|int, array<string, mixed>> $newNavigations
     *
     * @return array<string|int, array<string, mixed>>
     */
    protected function prepareNewNavigationsToApplying(array $newNavigations): array
    {
        $resultNewNavigations = [];
        $navigationDataReplacingMap = [
            self::NAVIGATION_DATA_KEY_MODULE => self::NAVIGATION_DATA_KEY_BUNDLE,
        ];

        foreach ($newNavigations as $navigationKey => $navigationData) {
            $newNavigationData = [];

            foreach ($navigationData as $navigationDataKey => $navigationDataValue) {
                if (array_key_exists($navigationDataKey, $navigationDataReplacingMap)) {
                    $newNavigationData[$navigationDataReplacingMap[$navigationDataKey]] = $navigationDataValue;

                    continue;
                }

                $newNavigationData[$navigationDataKey] = $navigationDataValue;
            }

            $resultNewNavigations[$navigationKey] = $newNavigationData;

            if (!isset($resultNewNavigations[$navigationKey][self::NAVIGATION_DATA_KEY_PAGES])) {
                continue;
            }

            $resultNewNavigations[$navigationKey][self::NAVIGATION_DATA_KEY_PAGES] = $this->prepareNewNavigationsToApplying(
                $resultNewNavigations[$navigationKey][self::NAVIGATION_DATA_KEY_PAGES]
            );
        }

        return $resultNewNavigations;
    }

    /**
     * @param array<string|int, array<string, mixed>> $navigation
     * @param array<string|int, array<string, mixed>> $newNavigations
     *
     * @return array<string|int, array<string, mixed>>
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
     * @param array<string|int, array<string, mixed>> $navigation
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $inputOutput
     * @param bool $isDry
     *
     * @return bool
     */
    protected function writeNavigationSchema(array $navigation, InputOutputInterface $inputOutput, bool $isDry): bool
    {
        $navigationXmlString = $this->getXmlNavigationFromArrayNavigation($navigation)->asXML();

        if ($isDry) {
            $inputOutput->writeln($navigationXmlString ?: '');

            return true;
        }

        if ($navigationXmlString === false) {
            return false;
        }

        $navigationXmlDomDocument = new DOMDocument('1.0');
        $navigationXmlDomDocument->preserveWhiteSpace = false;
        $navigationXmlDomDocument->formatOutput = true;
        $navigationXmlDomDocument->loadXML($navigationXmlString);

        $callback = function ($matches) {
            $multiplier = (int)(strlen($matches[1]) / 2) * 4;

            return str_repeat(' ', $multiplier) . '<';
        };

        $navigationXmlString = $navigationXmlDomDocument->saveXML();

        if ($navigationXmlString === false) {
            return false;
        }

        $content = preg_replace_callback('/^( +)</m', $callback, $navigationXmlString);

        file_put_contents($this->getNavigationSourceFilePath(), $content);

        return true;
    }

    /**
     * @param array<string|int, array<string, mixed>> $navigation
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
     * @param array<string|int, array<string, mixed>> $navigation
     * @param SimpleXMLElement $parentXmlElement
     *
     * @return SimpleXMLElement
     */
    protected function transformNavigationToXmlElement(array $navigation, SimpleXMLElement $parentXmlElement): SimpleXMLElement
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
