<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\PartialParser;

use SprykerSdk\Integrator\Transfer\ExpressionPartialParserResultTransfer;

interface ExpressionPartialParserInterface
{
    /**
     * @param string $codeString
     *
     * @return \SprykerSdk\Integrator\Transfer\ExpressionPartialParserResultTransfer
     */
    public function parse(string $codeString): ExpressionPartialParserResultTransfer;
}
