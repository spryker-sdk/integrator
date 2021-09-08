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
use SprykerSdk\Shared\Integrator\IntegratorFactoryAwareTrait;
use Symfony\Component\Filesystem\Filesystem;

class BaseTestCase extends PHPUnitTestCase {

    use IntegratorFactoryAwareTrait;


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

    /**
     * @return Filesystem
     */
    public function createFilesystem(): Filesystem
    {
        return new Filesystem();
    }

    /**
     * @return string
     */
    public function getTempDirectoryPath(): string
    {
        return APPLICATION_ROOT_DIR;
    }

    /**
     * @return string
     */
    public function getDataDirectoryPath(): string
    {
        return ROOT_TESTS . DIRECTORY_SEPARATOR . DATA_DIRECTORY_NAME;
    }

    /**
     * @return string
     */
    public function getProjectMockPath(): string
    {
        return $this->getDataDirectoryPath() . DIRECTORY_SEPARATOR . 'project_mock';
    }

}
