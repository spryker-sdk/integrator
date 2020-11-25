<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerSdk\Zed\Integrator\Business;

use PhpParser\Lexer;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use SprykerSdk\Zed\Integrator\Business\Builder\ClassWriter\ClassFileWriter;
use SprykerSdk\Zed\Integrator\Business\Builder\ClassGenerator\ClassGenerator;
use SprykerSdk\Zed\Integrator\Business\Builder\ClassLoader\ClassLoader;
use SprykerSdk\Zed\Integrator\Business\Builder\ClassModifier\ClassConstantModifier;
use SprykerSdk\Zed\Integrator\Business\Builder\ClassModifier\ClassInstanceClassModifier;
use SprykerSdk\Zed\Integrator\Business\Builder\ClassModifier\ClassListModifier;
use SprykerSdk\Zed\Integrator\Business\Builder\ClassModifier\CommonClassModifier;
use SprykerSdk\Zed\Integrator\Business\Builder\ClassModifier\GlueRelationshipModifier;
use SprykerSdk\Zed\Integrator\Business\Builder\ClassResolver\ClassResolver;
use SprykerSdk\Zed\Integrator\Business\Builder\Finder\ClassNodeFinder;
use SprykerSdk\Zed\Integrator\Business\Builder\Printer\ClassDiffPrinter;
use SprykerSdk\Zed\Integrator\Business\Builder\Printer\ClassPrinter;
use SprykerSdk\Zed\Integrator\Business\Composer\ComposerLockReader;
use SprykerSdk\Zed\Integrator\Business\Executor\ManifestExecutor;
use SprykerSdk\Zed\Integrator\Business\Manifest\ManifestReader;
use SprykerSdk\Zed\Integrator\Business\ManifestStrategy\ConfigureEnvManifestStrategy;
use SprykerSdk\Zed\Integrator\Business\ManifestStrategy\ConfigureModuleManifestStrategy;
use SprykerSdk\Zed\Integrator\Business\ManifestStrategy\CopyModuleFileManifestStrategy;
use SprykerSdk\Zed\Integrator\Business\ManifestStrategy\ExecuteConsoleManifestStrategy;
use SprykerSdk\Zed\Integrator\Business\ManifestStrategy\ManifestStrategyInterface;
use SprykerSdk\Zed\Integrator\Business\ManifestStrategy\UnwireGlueRelationshipManifestStrategy;
use SprykerSdk\Zed\Integrator\Business\ManifestStrategy\UnwirePluginManifestStrategy;
use SprykerSdk\Zed\Integrator\Business\ManifestStrategy\UnwireWidgetManifestStrategy;
use SprykerSdk\Zed\Integrator\Business\ManifestStrategy\WireGlueRelationshipManifestStrategy;
use SprykerSdk\Zed\Integrator\Business\ManifestStrategy\WirePluginManifestStrategy;
use SprykerSdk\Zed\Integrator\Business\ManifestStrategy\WireWidgetManifestStrategy;
use SprykerSdk\Zed\Integrator\Business\SprykerLock\SprykerLockReader;
use SprykerSdk\Zed\Integrator\Business\SprykerLock\SprykerLockWriter;
use SprykerSdk\Zed\Integrator\IntegratorDependencyProvider;

/**
 * @method \SprykerSdk\Zed\Integrator\IntegratorConfig getConfig()
 */
class IntegratorBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \SprykerSdk\Zed\Integrator\Business\Executor\ManifestExecutor
     */
    public function creatManifestExecutor(): ManifestExecutor
    {
        return new ManifestExecutor(
            $this->createSprykerLockReader(),
            $this->createManifestReader(),
            $this->createSprykerLockWriter(),
            [
                $this->createWirePluginManifestExecutor(),
                $this->createUnwirePluginManifestExecutor(),
                $this->createWireWidgetManifestExecutor(),
                $this->createUnwireWidgetManifestExecutor(),
                $this->createConfigureModuleManifestExecutor(),
                $this->createCopyFileManifestExecutor(),
                $this->createConfigureEnvManifestExecutor(),
                $this->createWireGlueRelationshipManifestStrategy(),
                $this->createUnwireGlueRelationshipManifestStrategy(),
                $this->createExecuteConsoleManifestStrategy(),
            ]
        );
    }

    /**
     * @return \SprykerSdk\Zed\Integrator\Business\SprykerLock\SprykerLockReader
     */
    public function createSprykerLockReader(): SprykerLockReader
    {
        return new SprykerLockReader($this->getConfig());
    }

    /**
     * @return \SprykerSdk\Zed\Integrator\Business\SprykerLock\SprykerLockWriter
     */
    public function createSprykerLockWriter(): SprykerLockWriter
    {
        return new SprykerLockWriter($this->getConfig());
    }

    /**
     * @return \SprykerSdk\Zed\Integrator\Business\Composer\ComposerLockReader
     */
    public function createComposerLockReader(): ComposerLockReader
    {
        return new ComposerLockReader($this->getConfig());
    }

    /**
     * @return \SprykerSdk\Zed\Integrator\Business\Manifest\ManifestReader
     */
    public function createManifestReader(): ManifestReader
    {
        return new ManifestReader($this->createComposerLockReader(), $this->getConfig());
    }

    /**
     * @return \SprykerSdk\Zed\Integrator\Business\ManifestStrategy\ManifestStrategyInterface
     */
    public function createWirePluginManifestExecutor(): ManifestStrategyInterface
    {
        return new WirePluginManifestStrategy(
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerSdk\Zed\Integrator\Business\ManifestStrategy\ManifestStrategyInterface
     */
    public function createUnwirePluginManifestExecutor(): ManifestStrategyInterface
    {
        return new UnwirePluginManifestStrategy(
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerSdk\Zed\Integrator\Business\ManifestStrategy\ManifestStrategyInterface
     */
    public function createWireWidgetManifestExecutor(): ManifestStrategyInterface
    {
        return new WireWidgetManifestStrategy(
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerSdk\Zed\Integrator\Business\ManifestStrategy\UnwireWidgetManifestStrategy
     */
    public function createUnwireWidgetManifestExecutor(): UnwireWidgetManifestStrategy
    {
        return new UnwireWidgetManifestStrategy(
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerSdk\Zed\Integrator\Business\ManifestStrategy\ConfigureModuleManifestStrategy
     */
    public function createConfigureModuleManifestExecutor(): ConfigureModuleManifestStrategy
    {
        return new ConfigureModuleManifestStrategy(
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerSdk\Zed\Integrator\Business\ManifestStrategy\CopyModuleFileManifestStrategy
     */
    public function createCopyFileManifestExecutor(): CopyModuleFileManifestStrategy
    {
        return new CopyModuleFileManifestStrategy(
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerSdk\Zed\Integrator\Business\ManifestStrategy\ConfigureEnvManifestStrategy
     */
    public function createConfigureEnvManifestExecutor(): ConfigureEnvManifestStrategy
    {
        return new ConfigureEnvManifestStrategy(
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerSdk\Zed\Integrator\Business\ManifestStrategy\WireGlueRelationshipManifestStrategy
     */
    public function createWireGlueRelationshipManifestStrategy(): WireGlueRelationshipManifestStrategy
    {
        return new WireGlueRelationshipManifestStrategy($this->getConfig());
    }

    /**
     * @return \SprykerSdk\Zed\Integrator\Business\ManifestStrategy\UnwireGlueRelationshipManifestStrategy
     */
    public function createUnwireGlueRelationshipManifestStrategy(): UnwireGlueRelationshipManifestStrategy
    {
        return new UnwireGlueRelationshipManifestStrategy($this->getConfig());
    }

    /**
     * @return \SprykerSdk\Zed\Integrator\Business\ManifestStrategy\ExecuteConsoleManifestStrategy
     */
    public function createExecuteConsoleManifestStrategy(): ExecuteConsoleManifestStrategy
    {
        return new ExecuteConsoleManifestStrategy(
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerSdk\Zed\Integrator\Business\Builder\ClassWriter\ClassFileWriter
     */
    public function createClassFileWriter(): ClassFileWriter
    {
        return new ClassFileWriter($this->createClassPrinter());
    }

    /**
     * @return \SprykerSdk\Zed\Integrator\Business\Builder\Printer\ClassDiffPrinter
     */
    public function createClassDiffPrinter(): ClassDiffPrinter
    {
        return new ClassDiffPrinter($this->createClassPrinter());
    }

    /**
     * @return \SprykerSdk\Zed\Integrator\Business\Builder\Printer\ClassPrinter
     */
    public function createClassPrinter(): ClassPrinter
    {
        return new ClassPrinter();
    }

    /**
     * @return \SprykerSdk\Zed\Integrator\Business\Builder\ClassResolver\ClassResolver
     */
    public function createClassResolver(): ClassResolver
    {
        return new ClassResolver($this->createClassLoader(), $this->createClassGenerator());
    }

    /**
     * @return \SprykerSdk\Zed\Integrator\Business\Builder\ClassLoader\ClassLoader
     */
    public function createClassLoader(): ClassLoader
    {
        return new ClassLoader(
            $this->getPhpParserParser(),
            $this->getPhpParserLexer()
        );
    }

    /**
     * @return \SprykerSdk\Zed\Integrator\Business\Builder\ClassGenerator\ClassGenerator
     */
    public function createClassGenerator(): ClassGenerator
    {
        return new ClassGenerator(
            $this->createClassLoader(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerSdk\Zed\Integrator\Business\Builder\ClassModifier\ClassInstanceClassModifier
     */
    public function createClassInstanceClassModifier(): ClassInstanceClassModifier
    {
        return new ClassInstanceClassModifier(
            $this->getPhpParserNodeTraverser(),
            $this->createCommonClassModifier(),
            $this->createClassNodeFinder()
        );
    }

    /**
     * @return \SprykerSdk\Zed\Integrator\Business\Builder\ClassModifier\CommonClassModifier
     */
    public function createCommonClassModifier(): CommonClassModifier
    {
        return new CommonClassModifier(
            $this->createClassNodeFinder()
        );
    }

    /**
     * @return \SprykerSdk\Zed\Integrator\Business\Builder\ClassModifier\ClassListModifier
     */
    public function createClassListModifier(): ClassListModifier
    {
        return new ClassListModifier(
            $this->getPhpParserNodeTraverser(),
            $this->createCommonClassModifier(),
            $this->createClassNodeFinder()
        );
    }

    /**
     * @return \SprykerSdk\Zed\Integrator\Business\Builder\ClassModifier\ClassConstantModifier
     */
    public function createClassConstantModifier(): ClassConstantModifier
    {
        return new ClassConstantModifier(
            $this->createClassNodeFinder()
        );
    }

    /**
     * @return \SprykerSdk\Zed\Integrator\Business\Builder\ClassModifier\GlueRelationshipModifier
     */
    public function createGlueRelationshipModifier(): GlueRelationshipModifier
    {
        return new GlueRelationshipModifier(
            $this->getPhpParserNodeTraverser(),
            $this->createCommonClassModifier(),
            $this->createClassNodeFinder()
        );
    }

    /**
     * @return \PhpParser\NodeFinder
     */
    public function createPhpParserNodeFinder(): NodeFinder
    {
        return new NodeFinder();
    }

    /**
     * @return \SprykerSdk\Zed\Integrator\Business\Builder\Finder\ClassNodeFinder
     */
    public function createClassNodeFinder(): ClassNodeFinder
    {
        return new ClassNodeFinder();
    }

    /**
     * @return \PhpParser\Parser
     */
    public function getPhpParserParser(): Parser
    {
        return $this->getProvidedDependency(IntegratorDependencyProvider::PHP_PARSER_PARSER);
    }

    /**
     * @return \PhpParser\NodeTraverser
     */
    public function getPhpParserNodeTraverser(): NodeTraverser
    {
        return new NodeTraverser();
    }

    /**
     * @return \PhpParser\Lexer
     */
    public function getPhpParserLexer(): Lexer
    {
        return $this->getProvidedDependency(IntegratorDependencyProvider::PHP_PARSER_LEXER);
    }
}
