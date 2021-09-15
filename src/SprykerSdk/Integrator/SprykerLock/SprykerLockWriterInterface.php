<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator\SprykerLock;


interface SprykerLockWriterInterface
{
    /**
     * @param array $lockData
     *
     * @return int
     */
    public function storeLock(array $lockData): int;
}
