<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerSdk\Zed\Integrator;

use PhpParser\Lexer;
use PhpParser\Lexer\Emulative;
use PhpParser\Parser\Php7;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use SprykerSdk\Zed\Integrator\Dependency\Facade\IntegratorToModuleFinderFacadeBridge;

class IntegratorDependencyProvider extends AbstractBundleDependencyProvider
{
    public const FACADE_MODULE_FINDER = 'FACADE_MODULE_FINDER';
    public const PHP_PARSER_LEXER = 'PHP_PARSER_LEXER';
    public const PHP_PARSER_PARSER = 'PHP_PARSER_PARSER';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = parent::provideBusinessLayerDependencies($container);

        $container = $this->addPhpParser($container);
        $container = $this->addModuleFinderFacade($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideCommunicationLayerDependencies(Container $container): Container
    {
        $container = parent::provideCommunicationLayerDependencies($container);

        $container = $this->addModuleFinderFacade($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addPhpParser(Container $container): Container
    {
        $lexer = $this->createLexer();

        $container->set(static::PHP_PARSER_LEXER, function () use ($lexer) {
            return $lexer;
        });
        $container->set(static::PHP_PARSER_PARSER, function () use ($lexer) {
            return new Php7($lexer);
        });

        return $container;
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
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addModuleFinderFacade(Container $container): Container
    {
        $container[static::FACADE_MODULE_FINDER] = function (Container $container) {
            return new IntegratorToModuleFinderFacadeBridge(
                $container->getLocator()->moduleFinder()->facade()
            );
        };

        return $container;
    }
}
