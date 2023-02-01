# Integrator Tool
[![Build Status](https://github.com/spryker-sdk/integrator/workflows/CI/badge.svg?branch=master)](https://github.com/spryker-sdk/integrator/actions?query=workflow%3ACI+branch%3Amaster)
[![codecov](https://codecov.io/gh/spryker-sdk/integrator/branch/master/graph/badge.svg?token=l6Xj26Cqei)](https://codecov.io/gh/spryker-sdk/integrator)
[![Latest Stable Version](https://poser.pugx.org/spryker-sdk/integrator/v/stable.svg)](https://packagist.org/packages/spryker-sdk/integrator)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-8892BF.svg)](https://php.net/)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg?style=flat)](https://phpstan.org/)

Auto-migrate applications in regard to new core releases using module manifests.

## Installation

This is a development only "require-dev" library:
```
composer require --dev spryker-sdk/integrator
```

Available options:
- `--format` - Define the format of the command output, example: json

## Available commands

### module:manifest:run

Running the integrator in basic mode. Unapplied manifests will be downloaded from the [repo](https://github.com/spryker-sdk/integrator-manifests).
The command expects optional argument `module-list` to be set. With the argument, manifests will be applied only for modules that were specified.
```
integrator module:manifest:run <moduleNameA, moduleNameB (not required)>
```


### release-group:manifest:run

Running the integrator for specific release group. Unapplied manifests will be downloaded from the S3 bucket.
Result of manifest applying (git diff) will be uploaded to the same bucket.
The command expects required argument `release-group-id`.
The command expects optional argument `branch-to-compare`.
```
integrator release-group:manifest:run <release-group-id (required)> <branch-to-compare (optional)>
```
Please specify the next S3 credentials:
```
export INTEGRATOR_FILE_BUCKET_NAME=<>
export INTEGRATOR_FILE_BUCKET_CREDENTIALS_KEY=<>
export INTEGRATOR_FILE_BUCKET_CREDENTIALS_SECRET=<>
export INTEGRATOR_FILE_BUCKET_REGION=<>
```

## Documentation

See [Documentation](docs/).
