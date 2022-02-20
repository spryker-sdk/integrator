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
            //TODO::Should it (the injection of navigation into the nested exist navigation item) be implemented now?
            // Check cthe comment inside the getTargetNavigationXmlElement method.
//            if (strpos($navigationKey, '.') === false) {
//                $targetXmlElement = $mainNavigationXmlElement;
//            } else {
//                $targetXmlElement = $this->getTargetNavigationXmlElement($mainNavigationXmlElement, $navigationKey);
//                $navigationKey = explode('.', $navigationKey)[0];
//            }
            $targetXmlElement = $mainNavigationXmlElement;

            //TODO::check if navigation is present and skip if it is (check it in nested group of navigation)
            // if ($targetXmlElement has this navigation) {
            //     should we update navigation for existing method or just call `continue`?
            //     continue;
            // }

            $navigationXmlElement = $targetXmlElement->addChild($navigationKey);
            $this->buildChildNavigation($navigationData, $navigationXmlElement);
        }

        // find the place where the new navigation should be added
        // add navigation in the found place. If place was not found than and add in into the end of the main list

        // TODO::It saves without the needed format -spaces and new lines. Should we solve it and Hoq if someone know?
        $mainNavigationXmlElement->saveXML(static::TARGET_NAVIGATION_FILE);

        return true;
    }

    /**
     * @param array<string, string|array> $navigationData
     * @param \SimpleXMLElement $newXmlElement
     *
     * @return void
     */
    protected function buildChildNavigation(array $navigationData, SimpleXMLElement $newXmlElement): void
    {
        foreach ($navigationData as $navigationDataKey => $navigationDataValue) {
            if ($navigationDataKey === 'pages') {
                continue;
            }

            $newXmlElement->addChild($navigationDataKey, $navigationDataValue);
        }


        if (!isset($navigationData['pages']) || !is_array($navigationData['pages'])) {
            return;
        }

        foreach ($navigationData['pages'] as $navigationChildKey => $navigationChildData) {
            $childXmlElement = $newXmlElement->addChild($navigationChildKey);

            // Be careful, the empty SimpleXMLElement with `!` symbol return `true`.
            if (!$childXmlElement instanceof SimpleXMLElement) {
                continue;
            }

            $this->buildChildNavigation($navigationChildData, $childXmlElement);
        }
    }

    /**
     * @param \SimpleXMLElement $mainNavigationXmlElement
     * @param string $qualifiedNavigationNamespace
     *
     * @return \SimpleXMLElement|null
     */
    protected function getTargetNavigationXmlElement(SimpleXMLElement $mainNavigationXmlElement, string $qualifiedNavigationNamespace): ?SimpleXMLElement
    {
        $namespaceElements = explode('.', $qualifiedNavigationNamespace);
        $targetXmlElement = null;

        foreach ($namespaceElements as $namespaceElement) {
            if (end($namespaceElements) === $namespaceElement) {
                return $targetXmlElement;
            }

            //TODO:: This method does not allow to get children and no one another as well. If you know the way how to do it than implement this feature othervise
            // contact to Rene and decide should it be implemented now or can be postponed - the injection of navigation into the nested exist navigation item.
            $targetXmlElement = $mainNavigationXmlElement->children($namespaceElement);

            if ($targetXmlElement === null) {
                return null;
            }
        }

        return $targetXmlElement;
    }
}
