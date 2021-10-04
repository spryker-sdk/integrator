<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator;

use PhpParser\BuilderFactory;
use PhpParser\Lexer;
use PhpParser\Lexer\Emulative;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\Parser\Php7;
use SprykerSdk\Integrator\Builder\ClassGenerator\ClassGenerator;
use SprykerSdk\Integrator\Builder\ClassLoader\ClassLoader;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassConstantModifier;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceClassModifier;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassListModifier;
use SprykerSdk\Integrator\Builder\ClassModifier\CommonClassModifier;
use SprykerSdk\Integrator\Builder\ClassModifier\GlueRelationshipModifier;
use SprykerSdk\Integrator\Builder\ClassResolver\ClassResolver;
use SprykerSdk\Integrator\Builder\ClassWriter\ClassFileWriter;
use SprykerSdk\Integrator\Builder\Checker\ClassMethodChecker;
use SprykerSdk\Integrator\Builder\Checker\ClassMethodCheckerInterface;
use SprykerSdk\Integrator\Builder\Finder\ClassNodeFinder;
use SprykerSdk\Integrator\Builder\Printer\ClassDiffPrinter;
use SprykerSdk\Integrator\Builder\Printer\ClassPrinter;
use SprykerSdk\Integrator\Composer\ComposerLockReader;
use SprykerSdk\Integrator\Executor\ManifestExecutor;
use SprykerSdk\Integrator\Helper\ClassHelper;
use SprykerSdk\Integrator\Helper\ClassHelperInterface;
use SprykerSdk\Integrator\Manifest\ManifestReader;
use SprykerSdk\Integrator\ManifestStrategy\ConfigureEnvManifestStrategy;
use SprykerSdk\Integrator\ManifestStrategy\ConfigureModuleManifestStrategy;
use SprykerSdk\Integrator\ManifestStrategy\CopyModuleFileManifestStrategy;
use SprykerSdk\Integrator\ManifestStrategy\ExecuteConsoleManifestStrategy;
use SprykerSdk\Integrator\ManifestStrategy\ManifestStrategyInterface;
use SprykerSdk\Integrator\ManifestStrategy\UnwireGlueRelationshipManifestStrategy;
use SprykerSdk\Integrator\ManifestStrategy\UnwirePluginManifestStrategy;
use SprykerSdk\Integrator\ManifestStrategy\UnwireWidgetManifestStrategy;
use SprykerSdk\Integrator\ManifestStrategy\WireGlueRelationshipManifestStrategy;
use SprykerSdk\Integrator\ManifestStrategy\WirePluginManifestStrategy;
use SprykerSdk\Integrator\ManifestStrategy\WireWidgetManifestStrategy;
use SprykerSdk\Integrator\ModuleFinder\ModuleFinderFacade;
use SprykerSdk\Integrator\ModuleFinder\ModuleFinderFacadeInterface;
use SprykerSdk\Integrator\SprykerLock\SprykerLockReader;
use SprykerSdk\Integrator\SprykerLock\SprykerLockReaderInterface;
use SprykerSdk\Integrator\SprykerLock\SprykerLockWriter;
use SprykerSdk\Integrator\SprykerLock\SprykerLockWriterInterface;

class IntegratorFactory
{
    /**
     * @return \SprykerSdk\Integrator\IntegratorConfig
     */
    protected function getConfig(): IntegratorConfig
    {
        return IntegratorConfig::getInstance();
    }

    /**
     * @return \SprykerSdk\Integrator\Executor\ManifestExecutor
     */
    public function creatManifestExecutor(): ManifestExecutor
    {
        return new ManifestExecutor(
            $this->createSprykerLockReader(),
            $this->createManifestReader(),
            $this->createSprykerLockWriter(),
            $this->getManifestExecutors()
        );
    }

    /**
     * @return \SprykerSdk\Integrator\SprykerLock\SprykerLockReaderInterface
     */
    public function createSprykerLockReader(): SprykerLockReaderInterface
    {
        return new SprykerLockReader($this->getConfig());
    }

    /**
     * @return \SprykerSdk\Integrator\SprykerLock\SprykerLockWriterInterface
     */
    public function createSprykerLockWriter(): SprykerLockWriterInterface
    {
        return new SprykerLockWriter($this->getConfig());
    }

    /**
     * @return \SprykerSdk\Integrator\Business\Composer\ComposerLockReader
     */
    public function createComposerLockReader(): ComposerLockReader
    {
        return new ComposerLockReader($this->getConfig());
    }

    /**
     * @return \SprykerSdk\Integrator\Business\Manifest\ManifestReader
     */
    public function createManifestReader(): ManifestReader
    {
        return new ManifestReader($this->createComposerLockReader(), $this->getConfig());
    }

    /**
     * @return \SprykerSdk\Integrator\Business\ManifestStrategy\ManifestStrategyInterface
     */
    public function createWirePluginManifestExecutor(): ManifestStrategyInterface
    {
        return new WirePluginManifestStrategy(
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Business\ManifestStrategy\ManifestStrategyInterface
     */
    public function createUnwirePluginManifestExecutor(): ManifestStrategyInterface
    {
        return new UnwirePluginManifestStrategy(
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Business\ManifestStrategy\ManifestStrategyInterface
     */
    public function createWireWidgetManifestExecutor(): ManifestStrategyInterface
    {
        return new WireWidgetManifestStrategy(
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Business\ManifestStrategy\UnwireWidgetManifestStrategy
     */
    public function createUnwireWidgetManifestExecutor(): UnwireWidgetManifestStrategy
    {
        return new UnwireWidgetManifestStrategy(
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Business\ManifestStrategy\ConfigureModuleManifestStrategy
     */
    public function createConfigureModuleManifestExecutor(): ConfigureModuleManifestStrategy
    {
        return new ConfigureModuleManifestStrategy(
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Business\ManifestStrategy\CopyModuleFileManifestStrategy
     */
    public function createCopyFileManifestExecutor(): CopyModuleFileManifestStrategy
    {
        return new CopyModuleFileManifestStrategy(
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Business\ManifestStrategy\ConfigureEnvManifestStrategy
     */
    public function createConfigureEnvManifestExecutor(): ConfigureEnvManifestStrategy
    {
        return new ConfigureEnvManifestStrategy(
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Business\ManifestStrategy\WireGlueRelationshipManifestStrategy
     */
    public function createWireGlueRelationshipManifestStrategy(): WireGlueRelationshipManifestStrategy
    {
        return new WireGlueRelationshipManifestStrategy($this->getConfig());
    }

    /**
     * @return \SprykerSdk\Integrator\Business\ManifestStrategy\UnwireGlueRelationshipManifestStrategy
     */
    public function createUnwireGlueRelationshipManifestStrategy(): UnwireGlueRelationshipManifestStrategy
    {
        return new UnwireGlueRelationshipManifestStrategy($this->getConfig());
    }

    /**
     * @return \SprykerSdk\Integrator\Business\ManifestStrategy\ExecuteConsoleManifestStrategy
     */
    public function createExecuteConsoleManifestStrategy(): ExecuteConsoleManifestStrategy
    {
        return new ExecuteConsoleManifestStrategy(
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Business\Builder\ClassWriter\ClassFileWriter
     */
    public function createClassFileWriter(): ClassFileWriter
    {
        return new ClassFileWriter($this->createClassPrinter());
    }

    /**
     * @return \SprykerSdk\Integrator\Business\Builder\Printer\ClassDiffPrinter
     */
    public function createClassDiffPrinter(): ClassDiffPrinter
    {
        return new ClassDiffPrinter($this->createClassPrinter());
    }

    /**
     * @return \SprykerSdk\Integrator\Business\Builder\Printer\ClassPrinter
     */
    public function createClassPrinter(): ClassPrinter
    {
        return new ClassPrinter();
    }

    /**
     * @return \SprykerSdk\Integrator\Business\Builder\ClassResolver\ClassResolver
     */
    public function createClassResolver(): ClassResolver
    {
        return new ClassResolver($this->createClassLoader(), $this->createClassGenerator());
    }

    /**
     * @return \SprykerSdk\Integrator\Business\Builder\ClassLoader\ClassLoader
     */
    public function createClassLoader(): ClassLoader
    {
        $lexer = $this->createPhpParserLexer();

        return new ClassLoader(
            $this->createPhpParserParser($lexer),
            $lexer
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Business\Builder\ClassGenerator\ClassGenerator
     */
    public function createClassGenerator(): ClassGenerator
    {
        return new ClassGenerator(
            $this->createClassLoader(),
            $this->createClassHelper(),
            $this->createClassBuilderFactory(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Business\Builder\ClassModifier\ClassInstanceClassModifier
     */
    public function createClassInstanceClassModifier(): ClassInstanceClassModifier
    {
        return new ClassInstanceClassModifier(
            $this->createCommonClassModifier(),
            $this->createClassNodeFinder(),
            $this->createClassMethodChecker()
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Business\Builder\ClassModifier\CommonClassModifier
     */
    public function createCommonClassModifier(): CommonClassModifier
    {
        return new CommonClassModifier(
            $this->createClassNodeFinder(),
            $this->createClassMethodChecker()
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Business\Builder\ClassModifier\ClassListModifier
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
     * @return \SprykerSdk\Integrator\Business\Builder\ClassModifier\ClassConstantModifier
     */
    public function createClassConstantModifier(): ClassConstantModifier
    {
        return new ClassConstantModifier(
            $this->createClassNodeFinder()
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Business\Builder\ClassModifier\GlueRelationshipModifier
     */
    public function createGlueRelationshipModifier(): GlueRelationshipModifier
    {
        return new GlueRelationshipModifier(
            $this->getPhpParserNodeTraverser(),
            $this->createCommonClassModifier(),
            $this->createClassNodeFinder(),
            $this->createClassHelper(),
            $this->createClassBuilderFactory()
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
     * @return \SprykerSdk\Integrator\Business\Builder\Finder\ClassNodeFinder
     */
    public function createClassNodeFinder(): ClassNodeFinder
    {
        return new ClassNodeFinder();
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\Checker\ClassMethodCheckerInterface
     */
    public function createClassMethodChecker(): ClassMethodCheckerInterface
    {
        return new ClassMethodChecker();
    }

    /**
     * @return \PhpParser\BuilderFactory
     */
    public function createClassBuilderFactory(): BuilderFactory
    {
        return new BuilderFactory();
    }

    /**
     * @return \SprykerSdk\Integrator\Helper\ClassHelperInterface;
     */
    public function createClassHelper(): ClassHelperInterface
    {
        return new ClassHelper();
    }

    /**
     * @return \PhpParser\Parser
     */
    public function createPhpParserParser(?Lexer $lexer = null): Parser
    {
        if (!$lexer) {
            $lexer = $this->createPhpParserLexer();
        }

        return new Php7($lexer);
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
    public function createPhpParserLexer(): Lexer
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
     * @return \SprykerSdk\Integrator\ModuleFinder\ModuleFinderFacadeInterface
     */
    public function getModuleFinderFacade(): ModuleFinderFacadeInterface
    {
        return new ModuleFinderFacade();
    }

    /**
     * @return array<mixed>
     */
    public function getManifestExecutors(): array
    {
        return [
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
        ];
    }
}
