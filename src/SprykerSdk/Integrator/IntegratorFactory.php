<?php

declare(strict_types = 1);

namespace SprykerSdk\Integrator;

use PhpParser\Lexer;
use PhpParser\Lexer\Emulative;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\Parser\Php7;
use SprykerSdk\Integrator\Business\Builder\ClassWriter\ClassFileWriter;
use SprykerSdk\Integrator\Business\Builder\ClassGenerator\ClassGenerator;
use SprykerSdk\Integrator\Business\Builder\ClassLoader\ClassLoader;
use SprykerSdk\Integrator\Business\Builder\ClassModifier\ClassConstantModifier;
use SprykerSdk\Integrator\Business\Builder\ClassModifier\ClassInstanceClassModifier;
use SprykerSdk\Integrator\Business\Builder\ClassModifier\ClassListModifier;
use SprykerSdk\Integrator\Business\Builder\ClassModifier\CommonClassModifier;
use SprykerSdk\Integrator\Business\Builder\ClassModifier\GlueRelationshipModifier;
use SprykerSdk\Integrator\Business\Builder\ClassResolver\ClassResolver;
use SprykerSdk\Integrator\Business\Builder\Finder\ClassNodeFinder;
use SprykerSdk\Integrator\Business\Builder\Printer\ClassDiffPrinter;
use SprykerSdk\Integrator\Business\Builder\Printer\ClassPrinter;
use SprykerSdk\Integrator\Business\Composer\ComposerLockReader;
use SprykerSdk\Integrator\Business\Executor\ManifestExecutor;
use SprykerSdk\Integrator\Business\Manifest\ManifestReader;
use SprykerSdk\Integrator\Business\ManifestStrategy\ConfigureEnvManifestStrategy;
use SprykerSdk\Integrator\Business\ManifestStrategy\ConfigureModuleManifestStrategy;
use SprykerSdk\Integrator\Business\ManifestStrategy\CopyModuleFileManifestStrategy;
use SprykerSdk\Integrator\Business\ManifestStrategy\ExecuteConsoleManifestStrategy;
use SprykerSdk\Integrator\Business\ManifestStrategy\ManifestStrategyInterface;
use SprykerSdk\Integrator\Business\ManifestStrategy\UnwireGlueRelationshipManifestStrategy;
use SprykerSdk\Integrator\Business\ManifestStrategy\UnwirePluginManifestStrategy;
use SprykerSdk\Integrator\Business\ManifestStrategy\UnwireWidgetManifestStrategy;
use SprykerSdk\Integrator\Business\ManifestStrategy\WireGlueRelationshipManifestStrategy;
use SprykerSdk\Integrator\Business\ManifestStrategy\WirePluginManifestStrategy;
use SprykerSdk\Integrator\Business\ManifestStrategy\WireWidgetManifestStrategy;
use SprykerSdk\Integrator\Business\SprykerLock\SprykerLockReader;
use SprykerSdk\Integrator\Business\SprykerLock\SprykerLockWriter;
use SprykerSdk\ModuleFinder\Business\ModuleFinderFacade;
use SprykerSdk\ModuleFinder\Business\ModuleFinderFacadeInterface;

class IntegratorFactory
{
    /**
     * @return \SprykerSdk\Integrator\IntegratorConfig
     */
    protected function getConfig(): IntegratorConfig
    {
        $config = IntegratorConfig::getInstance();
        $config->loadConfig();

        return $config;
    }

    /**
     * @return \SprykerSdk\Integrator\Business\Executor\ManifestExecutor
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
     * @return \SprykerSdk\Integrator\Business\SprykerLock\SprykerLockReader
     */
    public function createSprykerLockReader(): SprykerLockReader
    {
        return new SprykerLockReader($this->getConfig());
    }

    /**
     * @return \SprykerSdk\Integrator\Business\SprykerLock\SprykerLockWriter
     */
    public function createSprykerLockWriter(): SprykerLockWriter
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
        return new ClassLoader(
            $this->getPhpParserParser(),
            $this->getPhpParserLexer()
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Business\Builder\ClassGenerator\ClassGenerator
     */
    public function createClassGenerator(): ClassGenerator
    {
        return new ClassGenerator(
            $this->createClassLoader(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Business\Builder\ClassModifier\ClassInstanceClassModifier
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
     * @return \SprykerSdk\Integrator\Business\Builder\ClassModifier\CommonClassModifier
     */
    public function createCommonClassModifier(): CommonClassModifier
    {
        return new CommonClassModifier(
            $this->createClassNodeFinder()
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
     * @return \SprykerSdk\Integrator\Business\Builder\Finder\ClassNodeFinder
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
        $lexer = $this->getPhpParserLexer();

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
    public function getPhpParserLexer(): Lexer
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
     * @return \SprykerSdk\ModuleFinder\Business\ModuleFinderFacadeInterface
     */
    public function getModuleFinderFacade(): ModuleFinderFacadeInterface
    {
        return new ModuleFinderFacade();
    }
}
