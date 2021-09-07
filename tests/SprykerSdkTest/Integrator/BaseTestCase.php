<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdkTest\Integrator;

use PhpParser\Lexer;
use PhpParser\Lexer\Emulative;
use PhpParser\Parser\Php7;
use PhpParser\Parser;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use SprykerSdk\Integrator\IntegratorConfig;

class BaseTestCase extends PHPUnitTestCase {

    /**
     * @return \SprykerSdk\Integrator\IntegratorConfig
     */
    public function getIntegratorConfig(): IntegratorConfig
    {
        return new IntegratorConfig();
    }

    /**
     * @return \PhpParser\Lexer
     */
    protected function createLexer(): Lexer
    {
        return new Emulative([
            'usedAttributes' => [
                'comments',
                'startLine', 'endLine',
                'startTokenPos', 'endTokenPos',
            ],
        ]);
    }

    /**
     * @return \PhpParser\Parser
     */
    public function createPhpParser(): Parser
    {
        return new Php7($this->createLexer());
    }
}
