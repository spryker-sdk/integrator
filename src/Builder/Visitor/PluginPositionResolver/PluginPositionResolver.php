<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator\Builder\Visitor\PluginPositionResolver;

class PluginPositionResolver implements PluginPositionResolverInterface
{
    /**
     * @param array<string> $existPlugins
     * @param array<string> $positionPlugins
     *
     * @return string|null
     */
    public function getFirstExistPluginByPositions(array $existPlugins, array $positionPlugins): ?string
    {
        $crossPlugins = [];

        foreach ($existPlugins as $plugin) {
            $position = array_search($plugin, $positionPlugins, true);
            if ($position !== false) {
                $crossPlugins[$position] = $plugin;
            }
        }

        return array_shift($crossPlugins) ?? array_shift($positionPlugins);
    }
}
