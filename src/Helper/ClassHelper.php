<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Helper;

class ClassHelper implements ClassHelperInterface
{
    /**
     * @param string $className
     *
     * @return string
     */
    public function getShortClassName(string $className): string
    {
        return ($pos = strrpos($className, '\\')) === false ? $className : substr($className, $pos + 1);
    }

    /**
     * @param string $className
     *
     * @return string
     */
    public function getClassNamespace(string $className): string
    {
        return ($pos = strrpos($className, '\\')) ? substr($className, 0, $pos) : '';
    }

    /**
     * @param string $className
     *
     * @return string
     */
    public function getOrganisationName(string $className): string
    {
        if (strrpos($className, '\\') === false) {
            return '';
        }

        return explode('\\', ltrim($className, '\\'))[0] ?? '';
    }

    /**
     * @param string $className
     *
     * @return string
     */
    public function getModuleName(string $className): string
    {
        if (strrpos($className, '\\') === false) {
            return '';
        }

        return explode('\\', ltrim($className, '\\'))[2] ?? '';
    }

    /**
     * @param string $className
     *
     * @return string
     */
    public function getLayerName(string $className): string
    {
        if (strrpos($className, '\\') === false) {
            return '';
        }

        return explode('\\', ltrim($className, '\\'))[1] ?? '';
    }
}
