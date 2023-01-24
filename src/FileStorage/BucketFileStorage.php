<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\FileStorage;

use Aws\Credentials\Credentials;
use Aws\S3\Exception\S3Exception;
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
    protected const NO_SUCH_KEY_ERROR_CODE = 'NoSuchKey';

    /**
     * @var string
     */
    protected const CONTENT_LENGTH_KEY = 'ContentLength';

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
     * @throws \RuntimeException
     *
     * @return string|null
     */
    public function getFile(string $filePath): ?string
    {
        $client = $this->getS3Client();
        try {
            $result = $client->getObject([
                static::BUCKET_KEY => $this->config->getFileBucketName(),
                static::OBJECT_KEY => $filePath,
            ]);
        } catch (S3Exception $exception) {
            $awsErrorCode = $exception->getAwsErrorCode();
            if ($awsErrorCode === static::NO_SUCH_KEY_ERROR_CODE) {
                return null;
            }

            throw $exception;
        }

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
        $this->validateCredentials();
        $credentials = new Credentials(
            $this->config->getFileBucketCredentialsKey(),
            $this->config->getFileBucketCredentialsSecret(),
        );

        return new S3Client([
            static::VERSION_KEY => static::LATEST_KEY,
            static::REGION_KEY => $this->config->getFileBucketRegion(),
            static::CREDENTIALS_KEY => $credentials,
        ]);
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
