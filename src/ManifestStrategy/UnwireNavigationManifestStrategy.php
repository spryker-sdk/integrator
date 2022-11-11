<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\ManifestStrategy;

use DOMDocument;
use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\Exception\UnexpectedNavigationXmlStructureException;

class UnwireNavigationManifestStrategy extends AbstractNavigationManifestStrategy
{
    /**
     * @return string
     */
    public function getType(): string
    {
        return 'unwire-navigation';
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
        );

        return $this->writeNavigationSchema($navigation, $inputOutput, $isDry);
    }

    /**
     * @param array<string|int, array<int|string, mixed>> $navigation
     * @param array<string|int, array<string, mixed|null>> $newNavigations
     *
     * @return array<string|int, array<int|string, mixed>>
     */
    protected function applyNewNavigation(
        array $navigation,
        array $newNavigations
    ): array {
        $outputDiff = [];

        foreach ($navigation as $key => $value) {
            if (array_key_exists($key, $newNavigations)) {
                if ($value !== null && $newNavigations[$key] === null) {
                    // Deleted element is found, do not add element to output array
                } elseif (is_array($value) && is_array($newNavigations[$key])) {
                    $recursiveDiff = $this->applyNewNavigation($value, $newNavigations[$key]);

                    if (count($recursiveDiff)) {
                        $outputDiff[$key] = $recursiveDiff;
                    }
                } elseif (!in_array($value, $newNavigations)) {
                    $outputDiff[$key] = $value;
                }
            } elseif (!in_array($value, $newNavigations)) {
                $outputDiff[$key] = $value;
            }
        }

        return $outputDiff;
    }

    /**
     * @param array<string|int, array<int|string, mixed>> $navigation
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
}
