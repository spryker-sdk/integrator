<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\ManifestStrategy;

use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;

class NavigationManifestStrategy extends AbstractManifestStrategy
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
        return 'navigation';
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
                    "navigation-item-configuration": "    <application-catalog-gui>\n        <label>Apps</label>\n        <title>Apps</title>\n        <icon>fa-archive</icon>\n        <bundle>application-catalog-gui</bundle>\n        <controller>index</controller>\n        <action>index</action>\n    </application-catalog-gui>",
                    "navigation-item-configuration-json": {
                        "application-catalog-gui": {
                        "label": "Apps",
                        "title": "Apps",
                        "icon": "fa-archive",
                        "bundle": "application-catalog-gui",
                        "controller": "index",
                        "action": "index"
                        }
                    },
                    "after": "users"
                }
            ]
        }

         */

        return true;
    }
}
