<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator;

use PhpParser\BuilderFactory;
use PhpParser\Lexer;
use PhpParser\Lexer\Emulative;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\Parser\Php7;
use PhpParser\ParserFactory;
use SprykerSdk\Integrator\Builder\ArgumentBuilder\ArgumentBuilder;
use SprykerSdk\Integrator\Builder\ArgumentBuilder\ArgumentBuilderInterface;
use SprykerSdk\Integrator\Builder\Checker\ClassMethodChecker;
use SprykerSdk\Integrator\Builder\Checker\ClassMethodCheckerInterface;
use SprykerSdk\Integrator\Builder\Checker\MethodStatementChecker\ArgsMethodStatementChecker;
use SprykerSdk\Integrator\Builder\Checker\MethodStatementChecker\ClassMethodStatementChecker;
use SprykerSdk\Integrator\Builder\Checker\MethodStatementChecker\ItemsMethodStatementChecker;
use SprykerSdk\Integrator\Builder\Checker\MethodStatementChecker\NameMethodStatementChecker;
use SprykerSdk\Integrator\Builder\Checker\MethodStatementChecker\PartsMethodStatementChecker;
use SprykerSdk\Integrator\Builder\ClassGenerator\ClassGenerator;
use SprykerSdk\Integrator\Builder\ClassGenerator\ClassGeneratorInterface;
use SprykerSdk\Integrator\Builder\ClassLoader\ClassLoader;
use SprykerSdk\Integrator\Builder\ClassLoader\ClassLoaderInterface;
use SprykerSdk\Integrator\Builder\ClassMetadataBuilder\ClassMetadataBuilder;
use SprykerSdk\Integrator\Builder\ClassMetadataBuilder\ClassMetadataBuilderInterface;
use SprykerSdk\Integrator\Builder\ClassMetadataBuilder\Plugin\ArgumentBuilderPlugin;
use SprykerSdk\Integrator\Builder\ClassMetadataBuilder\Plugin\IndexBuilderPlugin;
use SprykerSdk\Integrator\Builder\ClassMetadataBuilder\Plugin\PositionBuilderPlugin;
use SprykerSdk\Integrator\Builder\ClassMetadataBuilder\Plugin\SourceAndTargetBuilderPlugin;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassConstant\Modifier;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassConstant\ModifierInterface;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassConstant\Unwire\UnwireModifier as UnwireClassConstantModifier;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassConstant\Unwire\UnwireModifierInterface as UnwireClassConstantModifierInterface;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassConstant\Wire\WireModifier as WireClassConstantModifier;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassConstant\Wire\WireModifierInterface as WireClassConstantModifierInterface;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstance\Unwire\UnwireModifier as UnwireClassInstanceModifier;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstance\Unwire\UnwireModifierInterface as UnwireClassInstanceModifierInterface;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstance\Wire\WireModifier as WireClassInstanceModifier;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstance\Wire\WireModifierInterface as WireClassInstanceModifierInterface;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Applicable\ApplicableInterface;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Applicable\ApplicableReturnArrayModifierStrategy;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Applicable\ApplicableReturnChainedCollectionModifierStrategy;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Applicable\ApplicableReturnClassModifierStrategy;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Applicable\ApplicableReturnCollectionModifierStrategy;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Applicable\ApplicableReturnExtendContainerModifierStrategy;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Unwire\UnwireModifierStrategyInterface;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Unwire\UnwireReturnArrayModifierStrategy;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Unwire\UnwireReturnChainedCollectionModifierStrategy;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Unwire\UnwireReturnClassModifierStrategy;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Unwire\UnwireReturnCollectionModifierStrategy;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Unwire\UnwireReturnExtendContainerModifierStrategy;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Wire\WireModifierStrategyInterface;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Wire\WireReturnArrayModifierStrategy;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Wire\WireReturnChainedCollectionModifierStrategy;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Wire\WireReturnClassModifierStrategy;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Wire\WireReturnCollectionModifierStrategy;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Wire\WireReturnExtendContainerModifierStrategy;
use SprykerSdk\Integrator\Builder\ClassModifier\CommonClass\CommonClassModifier;
use SprykerSdk\Integrator\Builder\ClassModifier\CommonClass\CommonClassModifierInterface;
use SprykerSdk\Integrator\Builder\ClassModifier\GlueRelationship\Unwire\UnwireModifier as UnwireGlueRelationshipModifier;
use SprykerSdk\Integrator\Builder\ClassModifier\GlueRelationship\Unwire\UnwireModifierInterface as UnwireGlueRelationshipModifierInterface;
use SprykerSdk\Integrator\Builder\ClassModifier\GlueRelationship\Wire\WireModifier as WireGlueRelationshipModifier;
use SprykerSdk\Integrator\Builder\ClassModifier\GlueRelationship\Wire\WireModifierInterface as WireGlueRelationshipModifierInterface;
use SprykerSdk\Integrator\Builder\ClassResolver\ClassResolver;
use SprykerSdk\Integrator\Builder\ClassResolver\ClassResolverInterface;
use SprykerSdk\Integrator\Builder\ClassWriter\ClassFileWriter;
use SprykerSdk\Integrator\Builder\ClassWriter\ClassFileWriterInterface;
use SprykerSdk\Integrator\Builder\ConfigurationEnvironmentBuilder\ArrayConfigurationEnvironmentStrategy;
use SprykerSdk\Integrator\Builder\ConfigurationEnvironmentBuilder\BooleanConfigurationEnvironmentStrategy;
use SprykerSdk\Integrator\Builder\ConfigurationEnvironmentBuilder\ClassConfigurationEnvironmentStrategy;
use SprykerSdk\Integrator\Builder\ConfigurationEnvironmentBuilder\ConfigurationEnvironmentStrategyInterface;
use SprykerSdk\Integrator\Builder\ConfigurationEnvironmentBuilder\DefaultConfigurationEnvironmentStrategy;
use SprykerSdk\Integrator\Builder\ConfigurationEnvironmentBuilder\LiteralConfigurationEnvironmentStrategy;
use SprykerSdk\Integrator\Builder\ConfigurationEnvironmentBuilder\StringConfigurationEnvironmentStrategy;
use SprykerSdk\Integrator\Builder\Creator\MethodCreator;
use SprykerSdk\Integrator\Builder\Creator\MethodCreatorInterface;
use SprykerSdk\Integrator\Builder\Creator\MethodDocBlockCreator;
use SprykerSdk\Integrator\Builder\Creator\MethodDocBlockCreatorInterface;
use SprykerSdk\Integrator\Builder\Creator\MethodReturnTypeCreator;
use SprykerSdk\Integrator\Builder\Creator\MethodReturnTypeCreatorInterface;
use SprykerSdk\Integrator\Builder\Creator\MethodStatementsCreator;
use SprykerSdk\Integrator\Builder\Creator\MethodStatementsCreatorInterface;
use SprykerSdk\Integrator\Builder\Finder\ClassNodeFinder;
use SprykerSdk\Integrator\Builder\Finder\ClassNodeFinderInterface;
use SprykerSdk\Integrator\Builder\Printer\ClassDiffPrinter;
use SprykerSdk\Integrator\Builder\Printer\ClassDiffPrinterInterface;
use SprykerSdk\Integrator\Builder\Printer\ClassPrinter;
use SprykerSdk\Integrator\Composer\ComposerLockReader;
use SprykerSdk\Integrator\Composer\ComposerLockReaderInterface;
use SprykerSdk\Integrator\Executor\ManifestExecutor;
use SprykerSdk\Integrator\Executor\ManifestExecutorInterface;
use SprykerSdk\Integrator\Helper\ClassHelper;
use SprykerSdk\Integrator\Helper\ClassHelperInterface;
use SprykerSdk\Integrator\IntegratorLock\IntegratorLockReader;
use SprykerSdk\Integrator\IntegratorLock\IntegratorLockReaderInterface;
use SprykerSdk\Integrator\IntegratorLock\IntegratorLockWriter;
use SprykerSdk\Integrator\IntegratorLock\IntegratorLockWriterInterface;
use SprykerSdk\Integrator\Manifest\ManifestReader;
use SprykerSdk\Integrator\Manifest\ManifestReaderInterface;
use SprykerSdk\Integrator\ManifestStrategy\AddConfigArrayElementManifestStrategy;
use SprykerSdk\Integrator\ManifestStrategy\ConfigureEnvManifestStrategy;
use SprykerSdk\Integrator\ManifestStrategy\ConfigureModuleManifestStrategy;
use SprykerSdk\Integrator\ManifestStrategy\CopyModuleFileManifestStrategy;
use SprykerSdk\Integrator\ManifestStrategy\ExecuteConsoleManifestStrategy;
use SprykerSdk\Integrator\ManifestStrategy\GlossaryManifestStrategy;
use SprykerSdk\Integrator\ManifestStrategy\ManifestStrategyInterface;
use SprykerSdk\Integrator\ManifestStrategy\UnwireGlueRelationshipManifestStrategy;
use SprykerSdk\Integrator\ManifestStrategy\UnwirePluginManifestStrategy;
use SprykerSdk\Integrator\ManifestStrategy\UnwireWidgetManifestStrategy;
use SprykerSdk\Integrator\ManifestStrategy\WireGlueRelationshipManifestStrategy;
use SprykerSdk\Integrator\ManifestStrategy\WireNavigationManifestStrategy;
use SprykerSdk\Integrator\ManifestStrategy\WirePluginManifestStrategy;
use SprykerSdk\Integrator\ManifestStrategy\WireWidgetManifestStrategy;
use SprykerSdk\Integrator\ModuleFinder\ModuleFinderFacade;
use SprykerSdk\Integrator\ModuleFinder\ModuleFinderFacadeInterface;

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
     * @return \SprykerSdk\Integrator\Executor\ManifestExecutorInterface
     */
    public function creatManifestExecutor(): ManifestExecutorInterface
    {
        return new ManifestExecutor(
            $this->createIntegratorLockReader(),
            $this->createManifestReader(),
            $this->createIntegratorLockWriter(),
            $this->getManifestExecutorStrategies(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\IntegratorLock\IntegratorLockReaderInterface
     */
    public function createIntegratorLockReader(): IntegratorLockReaderInterface
    {
        return new IntegratorLockReader($this->getConfig());
    }

    /**
     * @return \SprykerSdk\Integrator\IntegratorLock\IntegratorLockWriterInterface
     */
    public function createIntegratorLockWriter(): IntegratorLockWriterInterface
    {
        return new IntegratorLockWriter($this->getConfig());
    }

    /**
     * @return \SprykerSdk\Integrator\Composer\ComposerLockReaderInterface
     */
    public function createComposerLockReader(): ComposerLockReaderInterface
    {
        return new ComposerLockReader($this->getConfig());
    }

    /**
     * @return \SprykerSdk\Integrator\Manifest\ManifestReaderInterface
     */
    public function createManifestReader(): ManifestReaderInterface
    {
        return new ManifestReader($this->createComposerLockReader(), $this->getConfig());
    }

    /**
     * @return \SprykerSdk\Integrator\ManifestStrategy\ManifestStrategyInterface
     */
    public function createWirePluginManifestStrategy(): ManifestStrategyInterface
    {
        return new WirePluginManifestStrategy(
            $this->getConfig(),
            $this->createClassHelper(),
            $this->createClassMetadataBuilder(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\ManifestStrategy\ManifestStrategyInterface
     */
    public function createUnwirePluginManifestStrategy(): ManifestStrategyInterface
    {
        return new UnwirePluginManifestStrategy(
            $this->getConfig(),
            $this->createClassHelper(),
            $this->createClassMetadataBuilder(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\ManifestStrategy\ManifestStrategyInterface
     */
    public function createAddConfigArrayElementManifestStrategy(): ManifestStrategyInterface
    {
        return new AddConfigArrayElementManifestStrategy(
            $this->getConfig(),
            $this->createClassHelper(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\ManifestStrategy\ManifestStrategyInterface
     */
    public function createWireWidgetManifestStrategy(): ManifestStrategyInterface
    {
        return new WireWidgetManifestStrategy(
            $this->getConfig(),
            $this->createClassHelper(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\ManifestStrategy\ManifestStrategyInterface
     */
    public function createUnwireWidgetManifestStrategy(): ManifestStrategyInterface
    {
        return new UnwireWidgetManifestStrategy(
            $this->getConfig(),
            $this->createClassHelper(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\ManifestStrategy\ManifestStrategyInterface
     */
    public function createConfigureModuleManifestStrategy(): ManifestStrategyInterface
    {
        return new ConfigureModuleManifestStrategy(
            $this->getConfig(),
            $this->createClassHelper(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\ManifestStrategy\ManifestStrategyInterface
     */
    public function createCopyFileManifestStrategy(): ManifestStrategyInterface
    {
        return new CopyModuleFileManifestStrategy(
            $this->getConfig(),
            $this->createClassHelper(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\ManifestStrategy\ManifestStrategyInterface
     */
    public function createConfigureEnvManifestStrategy(): ManifestStrategyInterface
    {
        return new ConfigureEnvManifestStrategy(
            $this->getConfig(),
            $this->createClassHelper(),
            $this->getConfigurationEnvironmentStrategies(),
        );
    }

    /**
     * @return array<\SprykerSdk\Integrator\Builder\ConfigurationEnvironmentBuilder\ConfigurationEnvironmentStrategyInterface>
     */
    public function getConfigurationEnvironmentStrategies(): array
    {
        return [
            $this->createStringConfigurationEnvironmentStrategy(),
            $this->createBooleanConfigurationEnvironmentStrategy(),
            $this->createArrayConfigurationEnvironmentStrategy(),
            $this->createClassConfigurationEnvironmentStrategy(),
            $this->createLiteralConfigurationEnvironmentStrategy(),
            $this->createDefaultConfigurationEnvironmentStrategy(),
        ];
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ConfigurationEnvironmentBuilder\ConfigurationEnvironmentStrategyInterface
     */
    public function createBooleanConfigurationEnvironmentStrategy(): ConfigurationEnvironmentStrategyInterface
    {
        return new BooleanConfigurationEnvironmentStrategy();
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ConfigurationEnvironmentBuilder\ConfigurationEnvironmentStrategyInterface
     */
    public function createClassConfigurationEnvironmentStrategy(): ConfigurationEnvironmentStrategyInterface
    {
        return new ClassConfigurationEnvironmentStrategy();
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ConfigurationEnvironmentBuilder\ConfigurationEnvironmentStrategyInterface
     */
    public function createLiteralConfigurationEnvironmentStrategy(): ConfigurationEnvironmentStrategyInterface
    {
        return new LiteralConfigurationEnvironmentStrategy();
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ConfigurationEnvironmentBuilder\ConfigurationEnvironmentStrategyInterface
     */
    public function createDefaultConfigurationEnvironmentStrategy(): ConfigurationEnvironmentStrategyInterface
    {
        return new DefaultConfigurationEnvironmentStrategy();
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ConfigurationEnvironmentBuilder\ConfigurationEnvironmentStrategyInterface
     */
    public function createStringConfigurationEnvironmentStrategy(): ConfigurationEnvironmentStrategyInterface
    {
        return new StringConfigurationEnvironmentStrategy();
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ConfigurationEnvironmentBuilder\ConfigurationEnvironmentStrategyInterface
     */
    public function createArrayConfigurationEnvironmentStrategy(): ConfigurationEnvironmentStrategyInterface
    {
        return new ArrayConfigurationEnvironmentStrategy();
    }

    /**
     * @return \SprykerSdk\Integrator\ManifestStrategy\ManifestStrategyInterface
     */
    public function createGlossaryManifestStrategy(): ManifestStrategyInterface
    {
        return new GlossaryManifestStrategy(
            $this->getConfig(),
            $this->createClassHelper(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\ManifestStrategy\ManifestStrategyInterface
     */
    public function createWireGlueRelationshipManifestStrategy(): ManifestStrategyInterface
    {
        return new WireGlueRelationshipManifestStrategy(
            $this->getConfig(),
            $this->createClassHelper(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\ManifestStrategy\ManifestStrategyInterface
     */
    public function createUnwireGlueRelationshipManifestStrategy(): ManifestStrategyInterface
    {
        return new UnwireGlueRelationshipManifestStrategy(
            $this->getConfig(),
            $this->createClassHelper(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\ManifestStrategy\ManifestStrategyInterface
     */
    public function createExecuteConsoleManifestStrategy(): ManifestStrategyInterface
    {
        return new ExecuteConsoleManifestStrategy(
            $this->getConfig(),
            $this->createClassHelper(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\ManifestStrategy\ManifestStrategyInterface
     */
    public function createNavigationManifestStrategy(): ManifestStrategyInterface
    {
        return new WireNavigationManifestStrategy(
            $this->getConfig(),
            $this->createClassHelper(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassMetadataBuilder\ClassMetadataBuilderInterface
     */
    public function createClassMetadataBuilder(): ClassMetadataBuilderInterface
    {
        return new ClassMetadataBuilder([
            new SourceAndTargetBuilderPlugin(),
            new ArgumentBuilderPlugin(),
            new PositionBuilderPlugin(),
            new IndexBuilderPlugin(),
        ]);
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassWriter\ClassFileWriterInterface
     */
    public function createClassFileWriter(): ClassFileWriterInterface
    {
        return new ClassFileWriter($this->createClassPrinter());
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\Printer\ClassDiffPrinterInterface
     */
    public function createClassDiffPrinter(): ClassDiffPrinterInterface
    {
        return new ClassDiffPrinter($this->createClassPrinter());
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\Printer\ClassPrinter
     */
    public function createClassPrinter(): ClassPrinter
    {
        return new ClassPrinter();
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassResolver\ClassResolverInterface
     */
    public function createClassResolver(): ClassResolverInterface
    {
        return new ClassResolver($this->createClassLoader(), $this->createClassGenerator());
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassLoader\ClassLoaderInterface
     */
    public function createClassLoader(): ClassLoaderInterface
    {
        $lexer = $this->createPhpParserLexer();

        return new ClassLoader(
            $this->createPhpParserParser($lexer),
            $lexer,
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassGenerator\ClassGeneratorInterface
     */
    public function createClassGenerator(): ClassGeneratorInterface
    {
        return new ClassGenerator(
            $this->createClassLoader(),
            $this->createClassHelper(),
            $this->createClassBuilderFactory(),
            $this->getConfig(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstance\Wire\WireModifierInterface
     */
    public function createWireClassInstanceModifier(): WireClassInstanceModifierInterface
    {
        return new WireClassInstanceModifier(
            $this->createCommonClassModifier(),
            $this->createClassNodeFinder(),
            $this->createClassMethodChecker(),
            $this->getWireClassInstanceModifierStrategies(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstance\Unwire\UnwireModifierInterface
     */
    public function createUnwireClassInstanceModifier(): UnwireClassInstanceModifierInterface
    {
        return new UnwireClassInstanceModifier(
            $this->createCommonClassModifier(),
            $this->createClassNodeFinder(),
            $this->createClassMethodChecker(),
            $this->getUnwireClassInstanceModifierStrategies(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\CommonClass\CommonClassModifierInterface
     */
    public function createCommonClassModifier(): CommonClassModifierInterface
    {
        return new CommonClassModifier(
            $this->createClassNodeFinder(),
            $this->createClassMethodChecker(),
            $this->createMethodBodyCreator(),
            $this->createNodeTreeCreator(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\Creator\MethodCreatorInterface
     */
    public function createMethodBodyCreator(): MethodCreatorInterface
    {
        return new MethodCreator(
            $this->createNodeTreeCreator(),
            $this->createMethodDocBlockCreator(),
            $this->createMethodReturnTypeCreator(),
            $this->createParserFactory(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\Creator\MethodDocBlockCreatorInterface
     */
    public function createMethodDocBlockCreator(): MethodDocBlockCreatorInterface
    {
        return new MethodDocBlockCreator();
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\Creator\MethodReturnTypeCreatorInterface
     */
    public function createMethodReturnTypeCreator(): MethodReturnTypeCreatorInterface
    {
        return new MethodReturnTypeCreator();
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\Creator\MethodStatementsCreatorInterface
     */
    public function createNodeTreeCreator(): MethodStatementsCreatorInterface
    {
        return new MethodStatementsCreator();
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassConstant\Wire\WireModifierInterface
     */
    public function createWireClassConstantModifier(): WireClassConstantModifierInterface
    {
        return new WireClassConstantModifier(
            $this->createCommonClassModifier(),
            $this->createClassNodeFinder(),
            $this->createClassMethodChecker(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassConstant\Unwire\UnwireModifierInterface
     */
    public function createUnwireClassConstantModifier(): UnwireClassConstantModifierInterface
    {
        return new UnwireClassConstantModifier(
            $this->createCommonClassModifier(),
            $this->createClassNodeFinder(),
            $this->createClassMethodChecker(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassConstant\ModifierInterface
     */
    public function createClassConstantModifier(): ModifierInterface
    {
        return new Modifier(
            $this->createClassNodeFinder(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\GlueRelationship\Wire\WireModifierInterface
     */
    public function createWireGlueRelationshipModifier(): WireGlueRelationshipModifierInterface
    {
        return new WireGlueRelationshipModifier(
            $this->getPhpParserNodeTraverser(),
            $this->createCommonClassModifier(),
            $this->createClassNodeFinder(),
            $this->createClassHelper(),
            $this->createClassBuilderFactory(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\GlueRelationship\Unwire\UnwireModifierInterface
     */
    public function createUnwireGlueRelationshipModifier(): UnwireGlueRelationshipModifierInterface
    {
        return new UnwireGlueRelationshipModifier(
            $this->getPhpParserNodeTraverser(),
            $this->createCommonClassModifier(),
            $this->createClassNodeFinder(),
            $this->createClassHelper(),
            $this->createClassBuilderFactory(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\Finder\ClassNodeFinderInterface
     */
    public function createClassNodeFinder(): ClassNodeFinderInterface
    {
        return new ClassNodeFinder();
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\Checker\ClassMethodCheckerInterface
     */
    public function createClassMethodChecker(): ClassMethodCheckerInterface
    {
        return new ClassMethodChecker(
            $this->getMethodStatementCheckers(),
            $this->createParserFactory(),
        );
    }

    /**
     * @return \PhpParser\ParserFactory
     */
    public function createParserFactory(): ParserFactory
    {
        return new ParserFactory();
    }

    /**
     * @return array<int, \SprykerSdk\Integrator\Builder\Checker\MethodStatementChecker\ArgsMethodStatementChecker|\SprykerSdk\Integrator\Builder\Checker\MethodStatementChecker\ClassMethodStatementChecker|\SprykerSdk\Integrator\Builder\Checker\MethodStatementChecker\ItemsMethodStatementChecker|\SprykerSdk\Integrator\Builder\Checker\MethodStatementChecker\NameMethodStatementChecker|\SprykerSdk\Integrator\Builder\Checker\MethodStatementChecker\PartsMethodStatementChecker>
     */
    public function getMethodStatementCheckers(): array
    {
        return [
            new NameMethodStatementChecker(),
            new PartsMethodStatementChecker(),
            new ClassMethodStatementChecker(),
            new ItemsMethodStatementChecker(),
            new ArgsMethodStatementChecker(),
        ];
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
     * @param \PhpParser\Lexer|null $lexer
     *
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
     * @return array<\SprykerSdk\Integrator\ManifestStrategy\ManifestStrategyInterface>
     */
    public function getManifestExecutorStrategies(): array
    {
        return [
            $this->createWirePluginManifestStrategy(),
            $this->createUnwirePluginManifestStrategy(),
            $this->createWireWidgetManifestStrategy(),
            $this->createUnwireWidgetManifestStrategy(),
            $this->createAddConfigArrayElementManifestStrategy(),
            $this->createConfigureModuleManifestStrategy(),
            $this->createCopyFileManifestStrategy(),
            $this->createConfigureEnvManifestStrategy(),
            $this->createWireGlueRelationshipManifestStrategy(),
            $this->createUnwireGlueRelationshipManifestStrategy(),
            $this->createExecuteConsoleManifestStrategy(),
            $this->createNavigationManifestStrategy(),
            $this->createGlossaryManifestStrategy(),
        ];
    }

    /**
     * @return array<\SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Wire\WireModifierStrategyInterface>
     */
    public function getWireClassInstanceModifierStrategies(): array
    {
        return [
            $this->createWireClassInstanceReturnArrayModifierStrategy(),
            $this->createWireClassInstanceReturnClassModifierStrategy(),
            $this->createWireClassInstanceReturnChainedCollectionModifierStrategy(),
            $this->createWireClassInstanceReturnCollectionModifierStrategy(),
            $this->createWireClassInstanceReturnExtendContainerModifierStrategy(),
        ];
    }

    /**
     * @return array<\SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Unwire\UnwireModifierStrategyInterface>
     */
    public function getUnwireClassInstanceModifierStrategies(): array
    {
        return [
            $this->createUnwireClassInstanceReturnArrayModifierStrategy(),
            $this->createUnwireClassInstanceReturnClassModifierStrategy(),
            $this->createUnwireClassInstanceReturnChainedCollectionModifierStrategy(),
            $this->createUnwireClassInstanceReturnCollectionModifierStrategy(),
            $this->createUnwireClassInstanceReturnExtendContainerModifierStrategy(),
        ];
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Wire\WireModifierStrategyInterface
     */
    public function createWireClassInstanceReturnArrayModifierStrategy(): WireModifierStrategyInterface
    {
        return new WireReturnArrayModifierStrategy(
            $this->createCheckApplicableClassInstanceReturnArrayModifierStrategy(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Wire\WireModifierStrategyInterface
     */
    public function createWireClassInstanceReturnCollectionModifierStrategy(): WireModifierStrategyInterface
    {
        return new WireReturnCollectionModifierStrategy(
            $this->createArgumentBuider(),
            $this->createCheckApplicableClassInstanceReturnCollectionModifierStrategy(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Wire\WireModifierStrategyInterface
     */
    public function createWireClassInstanceReturnExtendContainerModifierStrategy(): WireModifierStrategyInterface
    {
        return new WireReturnExtendContainerModifierStrategy(
            $this->createArgumentBuider(),
            $this->createCheckApplicableClassInstanceReturnExtendContainerModifierStrategy(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Wire\WireModifierStrategyInterface
     */
    public function createWireClassInstanceReturnChainedCollectionModifierStrategy(): WireModifierStrategyInterface
    {
        return new WireReturnChainedCollectionModifierStrategy(
            $this->createArgumentBuider(),
            $this->createCheckApplicableClassInstanceReturnChainedCollectionModifierStrategy(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Wire\WireModifierStrategyInterface
     */
    public function createWireClassInstanceReturnClassModifierStrategy(): WireModifierStrategyInterface
    {
        return new WireReturnClassModifierStrategy(
            $this->createCommonClassModifier(),
            $this->createCheckApplicableClassInstanceReturnClassModifierStrategy(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Unwire\UnwireModifierStrategyInterface
     */
    public function createUnwireClassInstanceReturnArrayModifierStrategy(): UnwireModifierStrategyInterface
    {
        return new UnwireReturnArrayModifierStrategy(
            $this->createCheckApplicableClassInstanceReturnArrayModifierStrategy(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Unwire\UnwireModifierStrategyInterface
     */
    public function createUnwireClassInstanceReturnCollectionModifierStrategy(): UnwireModifierStrategyInterface
    {
        return new UnwireReturnCollectionModifierStrategy(
            $this->createCheckApplicableClassInstanceReturnCollectionModifierStrategy(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Unwire\UnwireModifierStrategyInterface
     */
    public function createUnwireClassInstanceReturnExtendContainerModifierStrategy(): UnwireModifierStrategyInterface
    {
        return new UnwireReturnExtendContainerModifierStrategy(
            $this->createCheckApplicableClassInstanceReturnExtendContainerModifierStrategy(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Unwire\UnwireModifierStrategyInterface
     */
    public function createUnwireClassInstanceReturnChainedCollectionModifierStrategy(): UnwireModifierStrategyInterface
    {
        return new UnwireReturnChainedCollectionModifierStrategy(
            $this->createCheckApplicableClassInstanceReturnChainedCollectionModifierStrategy(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Unwire\UnwireModifierStrategyInterface
     */
    public function createUnwireClassInstanceReturnClassModifierStrategy(): UnwireModifierStrategyInterface
    {
        return new UnwireReturnClassModifierStrategy(
            $this->createCommonClassModifier(),
            $this->createCheckApplicableClassInstanceReturnClassModifierStrategy(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Applicable\ApplicableReturnArrayModifierStrategy
     */
    public function createCheckApplicableClassInstanceReturnArrayModifierStrategy(): ApplicableInterface
    {
        return new ApplicableReturnArrayModifierStrategy();
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Applicable\ApplicableReturnCollectionModifierStrategy
     */
    public function createCheckApplicableClassInstanceReturnCollectionModifierStrategy(): ApplicableInterface
    {
        return new ApplicableReturnCollectionModifierStrategy();
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Applicable\ApplicableReturnExtendContainerModifierStrategy
     */
    public function createCheckApplicableClassInstanceReturnExtendContainerModifierStrategy(): ApplicableInterface
    {
        return new ApplicableReturnExtendContainerModifierStrategy();
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Applicable\ApplicableReturnChainedCollectionModifierStrategy
     */
    public function createCheckApplicableClassInstanceReturnChainedCollectionModifierStrategy(): ApplicableInterface
    {
        return new ApplicableReturnChainedCollectionModifierStrategy();
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Applicable\ApplicableReturnClassModifierStrategy
     */
    public function createCheckApplicableClassInstanceReturnClassModifierStrategy(): ApplicableInterface
    {
        return new ApplicableReturnClassModifierStrategy();
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ArgumentBuilder\ArgumentBuilderInterface
     */
    public function createArgumentBuider(): ArgumentBuilderInterface
    {
        return new ArgumentBuilder($this->createClassBuilderFactory());
    }
}
