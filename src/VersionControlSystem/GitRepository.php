<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\VersionControlSystem;

use CzProject\GitPhp\GitRepository as CzGitRepository;

class GitRepository extends CzGitRepository
{
    /**
     * @param string $currentBranch
     * @param string $originalBranch
     *
     * @return string
     */
    public function getDiff(string $currentBranch, string $originalBranch): string
    {
        $gitDiffOutput = $this->execute(
            ['diff', $originalBranch, $currentBranch],
        );

        return implode(PHP_EOL, $gitDiffOutput);
    }

    /**
     * @param string $name
     *
     * @return void
     */
    public function deleteBranch(string $name): void
    {
        $this->execute(['branch', '-D', $name]);
    }
}
