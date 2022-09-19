<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator\Builder\Visitor\PluginPositionResolver;

interface PluginPositionResolverInterface
{
    /**
     * @param array<string> $existPlugins
     * @param array<string> $positionPlugins
     *
     * @return string|null
     */
    public function getFirstExistPluginByPositions(array $existPlugins, array $positionPlugins): ?string;
}
