<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Pyz\Zed\TestIntegratorDefault;

class TestIntegratorDefaultConfig
{
    public function getScalarValue()
    {
        return 'scalar_not_the_value_that_we_are_looking_for';
    }

    public function getLiteralValue()
    {
        return 'literal_not_the_value_that_we_are_looking_for';
    }
}
