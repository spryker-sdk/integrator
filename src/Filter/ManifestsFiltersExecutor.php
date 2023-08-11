<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Filter;

class ManifestsFiltersExecutor implements ManifestsFiltersExecutorInterface
{
    /**
     * @var array<\SprykerSdk\Integrator\Filter\ManifestsFilterInterface>
     */
    protected array $manifestsFilters;

    /**
     * @param array<\SprykerSdk\Integrator\Filter\ManifestsFilterInterface> $manifestsFilters
     */
    public function __construct(array $manifestsFilters)
    {
        $this->manifestsFilters = $manifestsFilters;
    }

    /**
     * @param array<mixed> $manifests
     *
     * @return array<mixed>
     */
    public function filterManifests(array $manifests): array
    {
        foreach ($this->manifestsFilters as $manifestsFilter) {
            $manifests = $manifestsFilter->filterManifests($manifests);
        }

        return $manifests;
    }
}
