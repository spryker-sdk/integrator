<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\FileStorage;

use Aws\Credentials\Credentials;
use Aws\S3\S3Client;
use RuntimeException;
use SprykerSdk\Integrator\IntegratorConfig;

class BucketFileStorage implements BucketFileStorageInterface
{
    /**
     * @var string
     */
    protected const BUCKET_KEY = 'Bucket';

    /**
     * @var string
     */
    protected const OBJECT_KEY = 'Key';

    /**
     * @var string
     */
    protected const BODY_KEY = 'Body';

    /**
     * @var string
     */
    protected const VERSION_KEY = 'version';

    /**
     * @var string
     */
    protected const LATEST_KEY = 'latest';

    /**
     * @var string
     */
    protected const REGION_KEY = 'region';

    /**
     * @var string
     */
    protected const CREDENTIALS_KEY = 'credentials';

    /**
     * @var string
     */
    protected const CONTENT_LENGTH_KEY = 'ContentLength';

    /**
     * @var \Aws\S3\S3Client $s3Client
     */
    protected ?S3Client $s3Client = null;

    /**
     * @var \SprykerSdk\Integrator\IntegratorConfig $config
     */
    protected IntegratorConfig $config;

    /**
     * @param \SprykerSdk\Integrator\IntegratorConfig $config
     */
    public function __construct(IntegratorConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $filePath
     *
     * @return string|null
     */
    public function getFile(string $filePath): ?string
    {
        $client = $this->getS3Client();
        $bucketName = $this->config->getFileBucketName();

        if (!$client->doesObjectExist($bucketName, $filePath)) {
            return null;
        }

        $result = $client->getObject([
            static::BUCKET_KEY => $bucketName,
            static::OBJECT_KEY => $filePath,
        ]);
        /** @var \GuzzleHttp\Psr7\Stream $body */
        $body = $result->get(static::BODY_KEY);

        return $body->read($result->get(static::CONTENT_LENGTH_KEY));
    }

    /**
     * @param string $filePath
     * @param string $fileData
     *
     * @return void
     */
    public function addFile(string $filePath, string $fileData): void
    {
        $this->getS3Client()->putObject([
            static::BUCKET_KEY => $this->config->getFileBucketName(),
            static::OBJECT_KEY => $filePath,
            static::BODY_KEY => $fileData,
        ]);
    }

    /**
     * @return \Aws\S3\S3Client
     */
    protected function getS3Client(): S3Client
    {
        if ($this->s3Client) {
            return $this->s3Client;
        }

        $this->validateCredentials();
        $credentials = new Credentials(
            $this->config->getFileBucketCredentialsKey(),
            $this->config->getFileBucketCredentialsSecret(),
        );

        $this->s3Client = new S3Client([
            static::VERSION_KEY => static::LATEST_KEY,
            static::REGION_KEY => $this->config->getFileBucketRegion(),
            static::CREDENTIALS_KEY => $credentials,
        ]);

        return $this->s3Client;
    }

    /**
     * @throws \RuntimeException
     *
     * @return void
     */
    protected function validateCredentials(): void
    {
        if (!$this->config->getFileBucketName()) {
            throw new RuntimeException(sprintf(
                'Environment variable "%s" is not set.',
                IntegratorConfig::INTEGRATOR_FILE_BUCKET_NAME,
            ));
        }

        if (!$this->config->getFileBucketCredentialsKey()) {
            throw new RuntimeException(sprintf(
                'Environment variable "%s" is not set.',
                IntegratorConfig::INTEGRATOR_FILE_BUCKET_CREDENTIALS_KEY,
            ));
        }

        if (!$this->config->getFileBucketCredentialsSecret()) {
            throw new RuntimeException(sprintf(
                'Environment variable "%s" is not set.',
                IntegratorConfig::INTEGRATOR_FILE_BUCKET_CREDENTIALS_SECRET,
            ));
        }

        if (!$this->config->getFileBucketRegion()) {
            throw new RuntimeException(sprintf(
                'Environment variable "%s" is not set.',
                IntegratorConfig::INTEGRATOR_FILE_BUCKET_REGION,
            ));
        }
    }
}
