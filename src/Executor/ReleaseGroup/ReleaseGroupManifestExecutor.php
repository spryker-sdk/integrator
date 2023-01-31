<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Executor\ReleaseGroup;

use RuntimeException;
use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\Executor\ManifestExecutorInterface;
use SprykerSdk\Integrator\FileStorage\BucketFileStorageInterface;
use SprykerSdk\Integrator\Manifest\FileBucketManifestReaderInterface;
use SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer;
use SprykerSdk\Integrator\VersionControlSystem\GitRepository;

class ReleaseGroupManifestExecutor implements ReleaseGroupManifestExecutorInterface
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
     * @param int $releaseGroupId
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $inputOutput
     * @param \SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    public function runReleaseGroupManifestExecution(
        int $releaseGroupId,
        InputOutputInterface $inputOutput,
        IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
    ): void {
        $manifests = $this->manifestReader->readManifests($releaseGroupId);
        $unappliedManifests = $this->manifestExecutor->findUnappliedManifests($manifests, []);
        if (!count($unappliedManifests)) {
            throw new RuntimeException(
                sprintf('No unapplied manifests found for release group id: %s', $releaseGroupId),
            );
        }

        $dry = $commandArgumentsTransfer->getIsDryOrFail();
        if (!$dry) {
            $this->prepareBranch();
        }

        $this->manifestExecutor->applyManifestList($unappliedManifests, $inputOutput, $commandArgumentsTransfer);

        if ($dry) {
            return;
        }

        $this->storeDiff($releaseGroupId, $inputOutput);
        $this->gitClean();

        return;
    }

    /**
     * @param int $releaseGroupId
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $inputOutput
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    protected function storeDiff(int $releaseGroupId, InputOutputInterface $inputOutput): void
    {
        if (!$this->gitRepository->hasChanges()) {
            $this->gitClean();

            throw new RuntimeException(sprintf('No changes from manifests related to release group %s', $releaseGroupId));
        }

        $this->gitRepository->addAllChanges();
        $this->gitRepository->commit('The commit was created by integrator');

        $gitDiffOutput = $this->gitRepository->getDiff(
            static::INTEGRATOR_RESULT_BRANCH_NAME,
            static::MASTER_BRANCH_NAME,
        );

        $this->bucketFileStorage->addFile($releaseGroupId . DIRECTORY_SEPARATOR . static::DIFF_TO_DISPLAY_FILE_NAME, $gitDiffOutput);
        $inputOutput->writeln($gitDiffOutput, InputOutputInterface::VERBOSE);
        $inputOutput->writeln(sprintf('%s was uploaded to the bucket', static::DIFF_TO_DISPLAY_FILE_NAME));
    }

    /**
     * @return void
     */
    protected function prepareBranch(): void
    {
        $this->gitRepository->checkout(static::MASTER_BRANCH_NAME);
        if (in_array(static::INTEGRATOR_RESULT_BRANCH_NAME, (array)$this->gitRepository->getBranches())) {
            $this->gitRepository->deleteBranch(static::INTEGRATOR_RESULT_BRANCH_NAME);
        }
        $this->gitRepository->createBranch(static::INTEGRATOR_RESULT_BRANCH_NAME, true);
    }

    /**
     * @return void
     */
    protected function gitClean(): void
    {
        $this->gitRepository->checkout(static::MASTER_BRANCH_NAME);
        $this->gitRepository->deleteBranch(static::INTEGRATOR_RESULT_BRANCH_NAME);
    }
}
