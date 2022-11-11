<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\ManifestStrategy;

use SimpleXMLElement;
use SprykerSdk\Integrator\Exception\UnexpectedNavigationXmlStructureException;

abstract class AbstractNavigationManifestStrategy extends AbstractManifestStrategy
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
     * @throws \SprykerSdk\Integrator\Exception\UnexpectedNavigationXmlStructureException
     *
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
     * @param array<string|int, array<int|string, mixed>> $navigation
     *
     * @return \SimpleXMLElement
     */
    protected function getXmlNavigationFromArrayNavigation(array $navigation): SimpleXMLElement
    {
        return $this->transformNavigationToXmlElement(
            $navigation,
            new SimpleXMLElement('<config/>'),
        );
    }

    /**
     * @param array<string|int, array<int|string, mixed>|string> $navigation
     * @param \SimpleXMLElement $parentXmlElement
     *
     * @return \SimpleXMLElement
     */
    protected function transformNavigationToXmlElement(array $navigation, SimpleXMLElement $parentXmlElement): SimpleXMLElement
    {
        foreach ($navigation as $navigationName => $navigationData) {
            if (is_int($navigationName) || (is_array($navigationData) && !$navigationData)) {
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
