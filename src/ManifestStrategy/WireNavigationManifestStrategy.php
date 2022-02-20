<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\ManifestStrategy;

use SimpleXMLElement;
use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;

class WireNavigationManifestStrategy extends AbstractManifestStrategy
{
    /**
     * @var string
     */
    protected const TARGET_NAVIGATION_FILE = 'config/Zed/navigation.xml';

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
        if (!file_exists(static::TARGET_NAVIGATION_FILE)) {
            $inputOutput->writeln(sprintf(
                'The project doesn\'n have the navigation source file: %s.',
                static::TARGET_NAVIGATION_FILE,
            ), InputOutputInterface::DEBUG);

            return false;
        }

        $mainNavigationXmlElement = simplexml_load_file(static::TARGET_NAVIGATION_FILE);

        foreach ($manifest['navigation'] as $navigationKey => $navigationData) {
            $navigationXmlElement = $this->buildChildNavigation($navigationData);

//            $mainNavigationXmlElement->addChild($navigationKey, $navigationXmlElement);
        }

        //prepare simpleSmlStructure from navifgation array
        //foreach parent elements and add them until

        $simpleXmlElement = simplexml_load_file(static::TARGET_NAVIGATION_FILE);
        $simpleXmlElement->
        dd($simpleXmlElement);


        //TODO::Should it be implemented now?
        // if it should be added in neested structure we sholud check if it is present at first otherwise add in to the end of main list

        // Get XML
        // parse XML (how can we process structure that are in XML? Do we have some parser in Spryker?)
        // check if navigation is present and skip if it is (check it in nested group of navigation)
        // find the place where the new navigation should be added
        // add navigation in the found place. If place was not found than and add in into the end of the main list
        // save changes into source navigation file


        // Should we use JSON structure or XML string?
        // It depends on approach that will be used during processing navigations structure.
        // I guess, we should parse XML into array to be able to easy manipulate with navigations.
        // How to test your code -  read README file in this PR https://github.com/spryker-sdk/integrator/pull/14/files

        // Proposed structure of navigations
        /**
        vendor/spryker-sdk/integrator/data/recipes/integrator-recipes-master/ApplicationCatalogGui/1.0.0/installer-manifest.json


        {
        "navigation": [
        {
        "navigation-configuration": {
        "application-catalog-gui": {
        "label": "Apps",
        "title": "Apps",
        "icon": "fa-archive",
        "bundle": "application-catalog-gui",
        "controller": "index",
        "action": "index",
        "pages": [
        "bla1": {
        "label": "bla1",
        "title": "bla1",
        "icon": "fa-archive",
        "bundle": "bla1",
        "controller": "bla",
        "action": "one"
        },
        "bla2": {
        "label": "bla2",
        "title": "bla2",
        "icon": "fa-archive",
        "bundle": "bla1",
        "controller": "bla",
        "action": "two",
        "pages": [
        ...
        ]
        }
        ]
        }
        },
        "after": "users"
        }
        ]
        }

         */

        return true;
    }

    /**
     * @param array<string, string|array> $navigationData
     *
     * @return \SimpleXMLElement
     */
    protected function buildChildNavigation(array $navigationData): SimpleXMLElement
    {
        $mainNavigationXmlElement = new SimpleXMLElement("<?xml version='1.0'?><name></name>");

        foreach ($navigationData as $navigationDataKey => $navigationDataValue) {
            if ($navigationDataKey === 'pages') {
                continue;
            }

            $mainNavigationXmlElement->addChild($navigationDataKey, $navigationDataValue);
        }

        if (!isset($navigationData['pages']) || !is_array($navigationData['pages'])) {
            return $mainNavigationXmlElement;
        }

        foreach ($navigationData['pages'] as $navigationChildKey => $navigationChildData) {
            $navigationXmlElement = $this->buildChildNavigation($navigationChildData);

            $mainNavigationXmlElement->addChild($navigationChildKey);
            $mainNavigationXmlElement->offsetGet()
        }

        return $mainNavigationXmlElement;
    }
}
