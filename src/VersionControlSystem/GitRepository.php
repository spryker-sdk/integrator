<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\VersionControlSystem;

use CzProject\GitPhp\GitException;
use CzProject\GitPhp\GitRepository as CzGitRepository;

class GitRepository extends CzGitRepository
{
    /**
     * @throws \CzProject\GitPhp\GitException
     *
     * @return string
     */
    public function getHeadHashCommit(): string
    {
        $branch = $this->extractFromCommand(['rev-parse', 'HEAD'], 'trim');
        if (is_array($branch)) {
            return $branch[0];
        }

        throw new GitException('Getting of current hash failed.');
    }

    /**
     * @param string $originalBranch
     * @param string $currentBranch
     *
     * @return string
     */
    public function getDiff(string $originalBranch, string $currentBranch): string
    {
        $gitDiffOutput = $this->execute(
            ['diff', $originalBranch . '..' . $currentBranch, '--ignore-blank-lines'],
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
