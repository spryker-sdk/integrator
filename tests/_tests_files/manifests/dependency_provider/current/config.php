<?php

use Spryker\Shared\Kernel\KernelConstants;

$config[KernelConstants::RESOLVABLE_CLASS_NAMES_CACHE_ENABLED] = true;

$config[KernelConstants::PROJECT_NAMESPACE]
    = 'Pyz';

$config[KernelConstants::VAR_BOOL_CAST_VALUE]
    = (bool)$config[KernelConstants::PROJECT_NAMESPACE];

$config[KernelConstants::FUNC_VALUE]
    = getenv('SOMEKEY');
