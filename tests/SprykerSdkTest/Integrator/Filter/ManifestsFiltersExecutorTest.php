<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator\Filter;

use PHPUnit\Framework\TestCase;
use SprykerSdk\Integrator\Filter\ManifestsFilterInterface;
use SprykerSdk\Integrator\Filter\ManifestsFiltersExecutor;

class ManifestsFiltersExecutorTest extends TestCase
{
    /**
     * @return void
     */
    public function testFilterManifestsShouldInvokeFilters(): void
    {
        //Arrange
        $manifestsFiltersExecutor = new ManifestsFiltersExecutor([
            $this->createManifestsFilterMock(),
            $this->createManifestsFilterMock(),
        ]);

        //Act
        $manifestsFiltersExecutor->filterManifests([]);
    }

    /**
     * @return \SprykerSdk\Integrator\Filter\ManifestsFilterInterface
     */
    protected function createManifestsFilterMock(): ManifestsFilterInterface
    {
        $manifestsFilter = $this->createMock(ManifestsFilterInterface::class);
        $manifestsFilter->expects($this->once())->method('filterManifests');

        return $manifestsFilter;
    }
}
