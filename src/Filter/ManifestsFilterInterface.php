<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Filter;

interface ManifestsFilterInterface
{
    /**
     * @param array<mixed> $manifests
     *
     * @return array<mixed>
     */
    public function filterManifests(array $manifests): array;
}
