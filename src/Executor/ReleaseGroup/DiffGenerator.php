<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Executor\ReleaseGroup;

use CzProject\GitPhp\GitException;
use RuntimeException;
use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\Executor\ManifestExecutorInterface;
use SprykerSdk\Integrator\FileStorage\BucketFileStorageInterface;
use SprykerSdk\Integrator\Manifest\FileBucketManifestReaderInterface;
use SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer;
use SprykerSdk\Integrator\VersionControlSystem\GitRepository;

class DiffGenerator implements DiffGeneratorInterface
{
    /**
     * @var string
     */
    protected const MASTER_BRANCH_NAME = 'master';

    /**
     * @var string
     */
    protected const INTEGRATOR_RESULT_BRANCH_NAME = 'integrator/release-group-manifest-run';

    /**
     * @var string
     */
    protected const DIFF_TO_DISPLAY_FILE_NAME = 'diff_to_display.diff';

    /**
     * @var int
     */
    protected const GIT_ERROR_CODE_BRANCH_NOT_EXISTS = 128;

    /**
     * @var \SprykerSdk\Integrator\Manifest\FileBucketManifestReaderInterface
     */
    protected FileBucketManifestReaderInterface $manifestReader;

    /**
     * @var \SprykerSdk\Integrator\FileStorage\BucketFileStorageInterface
     */
    protected BucketFileStorageInterface $bucketFileStorage;

    /**
     * @var \SprykerSdk\Integrator\Executor\ManifestExecutorInterface
     */
    protected ManifestExecutorInterface $manifestExecutor;

    /**
     * @var \SprykerSdk\Integrator\VersionControlSystem\GitRepository
     */
    protected GitRepository $gitRepository;

    /**
     * @param \SprykerSdk\Integrator\Manifest\FileBucketManifestReaderInterface $manifestReader
     * @param \SprykerSdk\Integrator\FileStorage\BucketFileStorageInterface $bucketFileStorage
     * @param \SprykerSdk\Integrator\Executor\ManifestExecutorInterface $manifestExecutor
     * @param \SprykerSdk\Integrator\VersionControlSystem\GitRepository $gitRepository
     */
    public function __construct(
        FileBucketManifestReaderInterface $manifestReader,
        BucketFileStorageInterface $bucketFileStorage,
        ManifestExecutorInterface $manifestExecutor,
        GitRepository $gitRepository
    ) {
        $this->manifestExecutor = $manifestExecutor;
        $this->bucketFileStorage = $bucketFileStorage;
        $this->manifestReader = $manifestReader;
        $this->gitRepository = $gitRepository;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $inputOutput
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    public function generateDiff(
        IntegratorCommandArgumentsTransfer $commandArgumentsTransfer,
        InputOutputInterface $inputOutput
    ): void {
        $currentBranchName = $this->gitRepository->getCurrentBranchName();
        $releaseGroupId = $commandArgumentsTransfer->getReleaseGroupIdOrFail();
        $manifests = $this->manifestReader->readManifests($releaseGroupId);
        $unappliedManifests = $this->manifestExecutor->findUnappliedManifests($manifests, []);
        if (!count($unappliedManifests)) {
            throw new RuntimeException(
                sprintf('No unapplied manifests found for release group id: %s', $releaseGroupId),
            );
        }

        try {
            $dry = $commandArgumentsTransfer->getIsDryOrFail();
            if (!$dry) {
                $this->prepareBranch();
            }

            $this->manifestExecutor->applyManifestList([], $unappliedManifests, $inputOutput, $commandArgumentsTransfer);

            if ($dry) {
                return;
            }

            $this->storeDiff($releaseGroupId, $currentBranchName, $commandArgumentsTransfer->getBranchToCompareOrFail(), $inputOutput);
            $this->gitClean($currentBranchName);
        } catch (GitException $exception) {
            throw new RuntimeException(
                sprintf(
                    'Git error %s %s %s %s',
                    $exception->getCode(),
                    $exception->getMessage(),
                    $exception->getRunnerResult() ? implode(PHP_EOL, $exception->getRunnerResult()->getOutput()) : '',
                    $exception->getRunnerResult() ? implode(PHP_EOL, $exception->getRunnerResult()->getErrorOutput()) : '',
                ),
            );
        }
    }

    /**
     * @param int $releaseGroupId
     * @param string $currentBranchName
     * @param string $branchToCompare
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $inputOutput
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    protected function storeDiff(
        int $releaseGroupId,
        string $currentBranchName,
        string $branchToCompare,
        InputOutputInterface $inputOutput
    ): void {
        if (!$this->gitRepository->hasChanges()) {
            $this->gitClean($currentBranchName);

            return;
        }

        $this->gitRepository->addAllChanges();
        $this->gitRepository->commit('The commit was created by integrator', ['-n']);

        try {
            $gitDiffOutput = $this->gitRepository->getDiff($branchToCompare, static::INTEGRATOR_RESULT_BRANCH_NAME);
        } catch (GitException $e) {
            if ($e->getCode() !== static::GIT_ERROR_CODE_BRANCH_NOT_EXISTS) {
                throw $e;
            }
            $gitDiffOutput = $this->gitRepository->getDiff('origin/' . $branchToCompare, static::INTEGRATOR_RESULT_BRANCH_NAME);
        }

        $this->bucketFileStorage->addFile($releaseGroupId . DIRECTORY_SEPARATOR . static::DIFF_TO_DISPLAY_FILE_NAME, $gitDiffOutput);
        $inputOutput->writeln($gitDiffOutput, InputOutputInterface::VERBOSE);
        $inputOutput->writeln(sprintf('%s was uploaded to the bucket', static::DIFF_TO_DISPLAY_FILE_NAME));
    }

    /**
     * @return void
     */
    protected function prepareBranch(): void
    {
        if (in_array(static::INTEGRATOR_RESULT_BRANCH_NAME, (array)$this->gitRepository->getBranches())) {
            $this->gitRepository->deleteBranch(static::INTEGRATOR_RESULT_BRANCH_NAME);
        }
        $this->gitRepository->createBranch(static::INTEGRATOR_RESULT_BRANCH_NAME, true);
    }

    /**
     * @param string $currentBranchName
     *
     * @return void
     */
    protected function gitClean(string $currentBranchName): void
    {
        $this->gitRepository->checkout($currentBranchName);
        $this->gitRepository->deleteBranch(static::INTEGRATOR_RESULT_BRANCH_NAME);
    }
}
