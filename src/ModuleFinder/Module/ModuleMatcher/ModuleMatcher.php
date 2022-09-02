<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\ModuleFinder\Module\ModuleMatcher;

use SprykerSdk\Integrator\Transfer\ModuleFilterTransfer;
use SprykerSdk\Integrator\Transfer\ModuleTransfer;
use SprykerSdk\Integrator\Transfer\OrganizationTransfer;

class ModuleMatcher implements ModuleMatcherInterface
{
    /**
     * @param \SprykerSdk\Integrator\Transfer\ModuleTransfer $moduleTransfer
     * @param \SprykerSdk\Integrator\Transfer\ModuleFilterTransfer $moduleFilterTransfer
     *
     * @return bool
     */
    public function matches(ModuleTransfer $moduleTransfer, ModuleFilterTransfer $moduleFilterTransfer): bool
    {
        return $this->matchesOrganization($moduleFilterTransfer, $moduleTransfer->getOrganizationOrFail())
            && $this->matchesApplication($moduleFilterTransfer, $moduleTransfer)
            && $this->matchesModule($moduleFilterTransfer, $moduleTransfer);
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ModuleFilterTransfer $moduleFilterTransfer
     * @param \SprykerSdk\Integrator\Transfer\OrganizationTransfer $organizationTransfer
     *
     * @return bool
     */
    protected function matchesOrganization(ModuleFilterTransfer $moduleFilterTransfer, OrganizationTransfer $organizationTransfer): bool
    {
        if ($moduleFilterTransfer->getOrganization() === null) {
            return true;
        }

        return $this->match($moduleFilterTransfer->getOrganization()->getNameOrFail(), $organizationTransfer->getNameOrFail());
    }

    /**
     * Modules can hold several applications. We return true of one of the applications in the current module
     * matches the requested one.
     *
     * @param \SprykerSdk\Integrator\Transfer\ModuleFilterTransfer $moduleFilterTransfer
     * @param \SprykerSdk\Integrator\Transfer\ModuleTransfer $moduleTransfer
     *
     * @return bool
     */
    protected function matchesApplication(ModuleFilterTransfer $moduleFilterTransfer, ModuleTransfer $moduleTransfer): bool
    {
        if ($moduleFilterTransfer->getApplication() === null) {
            return true;
        }

        foreach ($moduleTransfer->getApplications() as $applicationTransfer) {
            if ($this->match($moduleFilterTransfer->getApplication()->getNameOrFail(), $applicationTransfer->getNameOrFail())) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ModuleFilterTransfer $moduleFilterTransfer
     * @param \SprykerSdk\Integrator\Transfer\ModuleTransfer $moduleTransfer
     *
     * @return bool
     */
    protected function matchesModule(ModuleFilterTransfer $moduleFilterTransfer, ModuleTransfer $moduleTransfer): bool
    {
        if ($moduleFilterTransfer->getModule() === null) {
            return true;
        }

        return $this->match($moduleFilterTransfer->getModule()->getNameOrFail(), $moduleTransfer->getNameOrFail());
    }

    /**
     * @param string $search
     * @param string $given
     *
     * @return bool
     */
    protected function match(string $search, string $given): bool
    {
        if ($search === $given) {
            return true;
        }

        if (mb_strpos($search, '*') !== 0) {
            $search = '^' . $search;
        }

        if (mb_strpos($search, '*') === 0) {
            $search = mb_substr($search, 1);
        }

        if (mb_substr($search, -1) !== '*') {
            $search .= '$';
        }

        if (mb_substr($search, -1) === '*') {
            $search = mb_substr($search, 0, mb_strlen($search) - 1);
        }

        return (bool)preg_match(sprintf('/%s/', $search), $given);
    }
}
