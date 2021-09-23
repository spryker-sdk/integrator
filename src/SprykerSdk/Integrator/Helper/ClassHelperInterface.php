<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator\Helper;

interface ClassHelperInterface
{
    /**
     * @param string $className
     *
     * @return string
     */
    public function getShortClassName(string $className): string;

    /**
     * @param string $className
     *
     * @return string
     */
    public function getClassNamespace(string $className): string;

    /**
     * @param string $className
     *
     * @return string
     */
    public function getOrganisationName(string $className): string;

    /**
     * @param string $className
     *
     * @return string
     */
    public function getModuleName(string $className): string;

    /**
     * @param string $className
     *
     * @return string
     */
    public function getLayerName(string $className): string;
}
