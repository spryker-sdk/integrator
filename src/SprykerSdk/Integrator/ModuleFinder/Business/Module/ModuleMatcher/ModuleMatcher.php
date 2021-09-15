<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator\ModuleFinder\Business\Module\ModuleMatcher;

use SprykerSdk\Shared\Transfer\ModuleFilterTransfer;
use SprykerSdk\Shared\Transfer\ModuleTransfer;
use SprykerSdk\Shared\Transfer\OrganizationTransfer;

class ModuleMatcher implements ModuleMatcherInterface
{
    /**
     * @param \SprykerSdk\Shared\Transfer\ModuleTransfer $moduleTransfer
     * @param \SprykerSdk\Shared\Transfer\ModuleFilterTransfer $moduleFilterTransfer
     *
     * @return bool
     */
    public function matches(ModuleTransfer $moduleTransfer, ModuleFilterTransfer $moduleFilterTransfer): bool
    {
        $accepted = true;

        if (!$this->matchesOrganization($moduleFilterTransfer, $moduleTransfer->getOrganization())) {
            $accepted = false;
        }
        if (!$this->matchesApplication($moduleFilterTransfer, $moduleTransfer)) {
            $accepted = false;
        }
        if (!$this->matchesModule($moduleFilterTransfer, $moduleTransfer)) {
            $accepted = false;
        }

        return $accepted;
    }

    /**
     * @param \SprykerSdk\Shared\Transfer\ModuleFilterTransfer $moduleFilterTransfer
     * @param \SprykerSdk\Shared\Transfer\OrganizationTransfer $organizationTransfer
     *
     * @return bool
     */
    protected function matchesOrganization(ModuleFilterTransfer $moduleFilterTransfer, OrganizationTransfer $organizationTransfer): bool
    {
        if ($moduleFilterTransfer->getOrganization() === null) {
            return true;
        }

        return $this->match($moduleFilterTransfer->getOrganization()->getName(), $organizationTransfer->getName());
    }

    /**
     * Modules can hold several applications. We return true of one of the applications in the current module
     * matches the requested one.
     *
     * @param \SprykerSdk\Shared\Transfer\ModuleFilterTransfer $moduleFilterTransfer
     * @param \SprykerSdk\Shared\Transfer\ModuleTransfer $moduleTransfer
     *
     * @return bool
     */
    protected function matchesApplication(ModuleFilterTransfer $moduleFilterTransfer, ModuleTransfer $moduleTransfer): bool
    {
        if ($moduleFilterTransfer->getApplication() === null) {
            return true;
        }

        $applicationMatches = false;
        foreach ($moduleTransfer->getApplications() as $applicationTransfer) {
            if ($this->match($moduleFilterTransfer->getApplication()->getName(), $applicationTransfer->getName())) {
                $applicationMatches = true;
            }
        }

        return $applicationMatches;
    }

    /**
     * @param \SprykerSdk\Shared\Transfer\ModuleFilterTransfer $moduleFilterTransfer
     * @param \SprykerSdk\Shared\Transfer\ModuleTransfer $moduleTransfer
     *
     * @return bool
     */
    protected function matchesModule(ModuleFilterTransfer $moduleFilterTransfer, ModuleTransfer $moduleTransfer): bool
    {
        if ($moduleFilterTransfer->getModule() === null) {
            return true;
        }

        return $this->match($moduleFilterTransfer->getModule()->getName(), $moduleTransfer->getName());
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
