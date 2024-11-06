<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\ConfigReader;

use InvalidArgumentException;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;

class ConfigReader implements ConfigReaderInterface
{
    /**
     * @var \PhpParser\ParserFactory
     */
    protected $parserFactory;

    /**
     * @param \PhpParser\ParserFactory $parserFactory
     */
    public function __construct(ParserFactory $parserFactory)
    {
        $this->parserFactory = $parserFactory;
    }

    /**
     * @param string $configPath
     * @param array<string> $configKeys
     *
     * @return array<string, mixed>
     */
    public function read(string $configPath, array $configKeys): array
    {
        $configValuesVisitor = new ConfigValuesVisitor($configKeys);

        $traverser = new NodeTraverser();
        $traverser->addVisitor($configValuesVisitor);
        $traverser->traverse($this->getAst($configPath));

        return $configValuesVisitor->getFoundValues();
    }

    /**
     * @param string $configPath
     *
     * @throws \InvalidArgumentException
     *
     * @return array<\PhpParser\Node\Stmt>
     */
    protected function getAst(string $configPath): array
    {
        $configFileContent = $this->getFileContent($configPath);

        $parser = $this->parserFactory->createForHostVersion();

        $ast = $parser->parse($configFileContent);

        if ($ast === null) {
            throw new InvalidArgumentException(sprintf('Unable to parse `%s`', $configPath));
        }

        return $ast;
    }

    /**
     * @param string $configPath
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    protected function getFileContent(string $configPath): string
    {
        $content = file_get_contents($configPath);

        if ($content === false) {
            throw new InvalidArgumentException(sprintf('Unable to read file `%s`. Error: %s', $configPath, error_get_last()['message'] ?? '-'));
        }

        return $content;
    }
}
