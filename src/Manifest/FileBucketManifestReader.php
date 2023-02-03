<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Manifest;

use RuntimeException;
use SprykerSdk\Integrator\FileStorage\BucketFileStorageInterface;

class FileBucketManifestReader implements FileBucketManifestReaderInterface
{
    /**
     * @var string
     */
    protected const INSTALLER_MANIFEST_JSON = 'installer-manifest.json';

    protected BucketFileStorageInterface $bucketFileStorage;

    /**
     * @param \SprykerSdk\Integrator\FileStorage\BucketFileStorageInterface $bucketFileStorage
     */
    public function __construct(BucketFileStorageInterface $bucketFileStorage)
    {
        $this->bucketFileStorage = $bucketFileStorage;
    }

    /**
     * @param int $releaseGroupId
     *
     * @throws \RuntimeException
     *
     * @return array<string, array<string, array<string>>>
     */
    public function readManifests(int $releaseGroupId): array
    {
        $fileContent = $this->bucketFileStorage->getFile($this->getObjectKey($releaseGroupId));
        if (!$fileContent) {
            return [];
        }

        $manifests = json_decode($fileContent, true);
        if (!is_array($manifests)) {
            throw new RuntimeException(
                sprintf('Invalid manifest data, release group ID `%s`', $releaseGroupId),
            );
        }

        return $manifests;
    }

    /**
     * @param int $releaseGroupId
     *
     * @return string
     */
    protected function getObjectKey(int $releaseGroupId): string
    {
        return $releaseGroupId . DIRECTORY_SEPARATOR . static::INSTALLER_MANIFEST_JSON;
    }
}
