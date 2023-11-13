<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator;

use CzProject\GitPhp\Runners\CliRunner;
use GuzzleHttp\Client;
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
use SprykerSdk\Integrator\Builder\ClassModifier\ClassConstant\ClassConstantModifier;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassConstant\ClassConstantModifierInterface;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassConstant\Unwire\UnwireClassConstantModifier;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassConstant\Unwire\UnwireClassConstantModifierInterface;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassConstant\Wire\WireClassConstantModifier;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassConstant\Wire\WireClassConstantModifierInterface;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstance\Unwire\UnwireClassInstanceModifier;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstance\Unwire\UnwireClassInstanceModifierInterface;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstance\Wire\WireClassInstanceModifier;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstance\Wire\WireClassInstanceModifierInterface;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Applicable\ApplicableModifierStrategyInterface;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Applicable\ReturnArrayModifierApplicableModifierStrategy;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Applicable\ReturnChainedCollectionApplicableModifierStrategy;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Applicable\ReturnClassModifierApplicableModifierStrategy;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Applicable\ReturnCollectionApplicableModifierStrategy;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Applicable\ReturnExtendContainerApplicableModifierStrategy;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Unwire\ReturnArrayUnwireClassInstanceModifierStrategy;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Unwire\ReturnChainedCollectionUnwireClassInstanceModifierStrategy;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Unwire\ReturnClassUnwireClassInstanceModifierStrategy;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Unwire\ReturnCollectionUnwireClassInstanceModifierStrategy;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Unwire\ReturnExtendContainerUnwireClassInstanceModifierStrategy;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Unwire\UnwireClassInstanceModifierStrategyInterface;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Wire\ReturnArrayWireClassInstanceModifierStrategy;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Wire\ReturnChainedCollectionWireClassInstanceModifierStrategy;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Wire\ReturnClassWireClassInstanceModifierStrategy;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Wire\ReturnCollectionWireClassInstanceModifierStrategy;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Wire\ReturnExtendContainerWireClassInstanceModifierStrategy;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Wire\WireClassInstanceModifierStrategyInterface;
use SprykerSdk\Integrator\Builder\ClassModifier\CommonClass\CommonClassModifier;
use SprykerSdk\Integrator\Builder\ClassModifier\CommonClass\CommonClassModifierInterface;
use SprykerSdk\Integrator\Builder\ClassModifier\GlueRelationship\Unwire\UnwireGlueRelationshipModifier;
use SprykerSdk\Integrator\Builder\ClassModifier\GlueRelationship\Unwire\UnwireGlueRelationshipModifierInterface;
use SprykerSdk\Integrator\Builder\ClassModifier\GlueRelationship\Wire\WireGlueRelationshipModifier;
use SprykerSdk\Integrator\Builder\ClassModifier\GlueRelationship\Wire\WireGlueRelationshipModifierInterface;
use SprykerSdk\Integrator\Builder\ClassResolver\ClassResolver;
use SprykerSdk\Integrator\Builder\ClassResolver\ClassResolverInterface;
use SprykerSdk\Integrator\Builder\ClassWriter\ClassFileWriter;
use SprykerSdk\Integrator\Builder\ClassWriter\ClassFileWriterInterface;
use SprykerSdk\Integrator\Builder\ConfigurationEnvironmentBuilder\ArrayConfigurationEnvironmentStrategy;
use SprykerSdk\Integrator\Builder\ConfigurationEnvironmentBuilder\BooleanConfigurationEnvironmentStrategy;
use SprykerSdk\Integrator\Builder\ConfigurationEnvironmentBuilder\ClassConfigurationEnvironmentStrategy;
use SprykerSdk\Integrator\Builder\ConfigurationEnvironmentBuilder\ConfigurationEnvironmentStrategyInterface;
use SprykerSdk\Integrator\Builder\ConfigurationEnvironmentBuilder\ConstantConfigurationEnvironmentStrategy;
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
use SprykerSdk\Integrator\Builder\FileNormalizer\CodeSnifferCompositeNormalizer;
use SprykerSdk\Integrator\Builder\FileNormalizer\CodeSniffStyleFileNormalizer;
use SprykerSdk\Integrator\Builder\FileNormalizer\FileNormalizerInterface;
use SprykerSdk\Integrator\Builder\FileNormalizer\FileNormalizersExecutor;
use SprykerSdk\Integrator\Builder\FileNormalizer\FileNormalizersExecutorInterface;
use SprykerSdk\Integrator\Builder\FileNormalizer\PhpCSFixerFileNormalizer;
use SprykerSdk\Integrator\Builder\FileStorage\FileStorageFactory;
use SprykerSdk\Integrator\Builder\FileStorage\FileStorageInterface;
use SprykerSdk\Integrator\Builder\Finder\ClassConstantFinder;
use SprykerSdk\Integrator\Builder\Finder\ClassConstantFinderInterface;
use SprykerSdk\Integrator\Builder\Finder\ClassNodeFinder;
use SprykerSdk\Integrator\Builder\Finder\ClassNodeFinderInterface;
use SprykerSdk\Integrator\Builder\PartialParser\ExpressionPartialParser;
use SprykerSdk\Integrator\Builder\PartialParser\ExpressionPartialParserInterface;
use SprykerSdk\Integrator\Builder\Printer\ClassDiffPrinter;
use SprykerSdk\Integrator\Builder\Printer\ClassDiffPrinterInterface;
use SprykerSdk\Integrator\Builder\Printer\ClassPrinter;
use SprykerSdk\Integrator\Builder\Resolver\PrefixedConstNameResolver;
use SprykerSdk\Integrator\Builder\Resolver\PrefixedConstNameResolverInterface;
use SprykerSdk\Integrator\Builder\Visitor\PluginPositionResolver\PluginPositionResolver;
use SprykerSdk\Integrator\Builder\Visitor\PluginPositionResolver\PluginPositionResolverInterface;
use SprykerSdk\Integrator\Communication\ReleaseApp\ModuleRatingFetcher;
use SprykerSdk\Integrator\Communication\ReleaseApp\ModuleRatingFetcherInterface;
use SprykerSdk\Integrator\Communication\ReleaseApp\ModuleRatingResponseMapper;
use SprykerSdk\Integrator\Composer\ComposerLockReader;
use SprykerSdk\Integrator\Composer\ComposerLockReaderInterface;
use SprykerSdk\Integrator\Configuration\ConfigurationProvider;
use SprykerSdk\Integrator\Configuration\ConfigurationProviderInterface;
use SprykerSdk\Integrator\Executor\ManifestExecutor;
use SprykerSdk\Integrator\Executor\ManifestExecutorInterface;
use SprykerSdk\Integrator\Executor\Module\ModuleManifestExecutor;
use SprykerSdk\Integrator\Executor\Module\ModuleManifestExecutorInterface;
use SprykerSdk\Integrator\Executor\ReleaseGroup\DiffGenerator;
use SprykerSdk\Integrator\FileStorage\BucketFileStorage;
use SprykerSdk\Integrator\FileStorage\BucketFileStorageInterface;
use SprykerSdk\Integrator\Filter\ManifestsFiltersExecutor;
use SprykerSdk\Integrator\Filter\ManifestsFiltersExecutorInterface;
use SprykerSdk\Integrator\Filter\RatingBasedManifestFilter\ManifestToModulesRatingRequestMapper;
use SprykerSdk\Integrator\Filter\RatingBasedManifestFilter\RatingBasedManifestsFilter;
use SprykerSdk\Integrator\Helper\ClassHelper;
use SprykerSdk\Integrator\Helper\ClassHelperInterface;
use SprykerSdk\Integrator\IntegratorLock\IntegratorLockReader;
use SprykerSdk\Integrator\IntegratorLock\IntegratorLockReaderInterface;
use SprykerSdk\Integrator\IntegratorLock\IntegratorLockWriter;
use SprykerSdk\Integrator\IntegratorLock\IntegratorLockWriterInterface;
use SprykerSdk\Integrator\Manifest\FileBucketManifestReader;
use SprykerSdk\Integrator\Manifest\FileBucketManifestReaderInterface;
use SprykerSdk\Integrator\Manifest\RepositoryManifestReaderInterface;
use SprykerSdk\Integrator\Manifest\RepositoryRepositoryManifestReader;
use SprykerSdk\Integrator\ManifestStrategy\AddConfigArrayElementManifestStrategy;
use SprykerSdk\Integrator\ManifestStrategy\ConfigureEnvManifestStrategy;
use SprykerSdk\Integrator\ManifestStrategy\ConfigureModuleManifestStrategy;
use SprykerSdk\Integrator\ManifestStrategy\CopyModuleFileManifestStrategy;
use SprykerSdk\Integrator\ManifestStrategy\ExecuteConsoleManifestStrategy;
use SprykerSdk\Integrator\ManifestStrategy\GlossaryManifestStrategy;
use SprykerSdk\Integrator\ManifestStrategy\ManifestStrategyInterface;
use SprykerSdk\Integrator\ManifestStrategy\UnwireGlueRelationshipManifestStrategy;
use SprykerSdk\Integrator\ManifestStrategy\UnwireNavigationManifestStrategy;
use SprykerSdk\Integrator\ManifestStrategy\UnwirePluginManifestStrategy;
use SprykerSdk\Integrator\ManifestStrategy\UnwireWidgetManifestStrategy;
use SprykerSdk\Integrator\ManifestStrategy\WireGlueRelationshipManifestStrategy;
use SprykerSdk\Integrator\ManifestStrategy\WireNavigationManifestStrategy;
use SprykerSdk\Integrator\ManifestStrategy\WirePluginManifestStrategy;
use SprykerSdk\Integrator\ManifestStrategy\WireSchemaManifestStrategy;
use SprykerSdk\Integrator\ManifestStrategy\WireTransferManifestStrategy;
use SprykerSdk\Integrator\ManifestStrategy\WireWidgetManifestStrategy;
use SprykerSdk\Integrator\VersionControlSystem\GitRepository;
use SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerService;
use SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface;
use Symfony\Component\Filesystem\Filesystem;

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
     * @return \SprykerSdk\Integrator\Executor\Module\ModuleManifestExecutorInterface
     */
    public function createModuleManifestExecutor(): ModuleManifestExecutorInterface
    {
        return new ModuleManifestExecutor(
            $this->createIntegratorLockReader(),
            $this->createIntegratorLockWriter(),
            $this->createRepositoryManifestReader(),
            $this->createManifestExecutor(),
            $this->createComposerLockReader(),
            $this->createManifestsFiltersExecutor(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Executor\ReleaseGroup\DiffGenerator
     */
    public function createReleaseGroupManifestExecutor(): DiffGenerator
    {
        return new DiffGenerator(
            $this->createFileBucketManifestReader(),
            $this->createBucketFileStorage(),
            $this->createManifestExecutor(),
            $this->createGitRepository(),
        );
    }

    /**
     * @param string|null $pathToRepository
     *
     * @return \SprykerSdk\Integrator\VersionControlSystem\GitRepository
     */
    public function createGitRepository(?string $pathToRepository = null): GitRepository
    {
        return new GitRepository($pathToRepository ?? APPLICATION_ROOT_DIR, new CliRunner());
    }

    /**
     * @return \SprykerSdk\Integrator\Executor\ManifestExecutorInterface
     */
    public function createManifestExecutor(): ManifestExecutorInterface
    {
        return new ManifestExecutor(
            $this->createFileNormalizersExecutor(),
            $this->getManifestStrategies(),
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
     * @return \SprykerSdk\Integrator\Manifest\RepositoryManifestReaderInterface
     */
    public function createRepositoryManifestReader(): RepositoryManifestReaderInterface
    {
        return new RepositoryRepositoryManifestReader($this->createComposerLockReader(), $this->getConfig());
    }

    /**
     * @return \SprykerSdk\Integrator\Manifest\FileBucketManifestReaderInterface
     */
    public function createFileBucketManifestReader(): FileBucketManifestReaderInterface
    {
        return new FileBucketManifestReader(
            $this->createBucketFileStorage(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\FileStorage\BucketFileStorageInterface
     */
    public function createBucketFileStorage(): BucketFileStorageInterface
    {
        return new BucketFileStorage($this->getConfig());
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
            $this->createClassLoader(),
            $this->createClassNodeFinder(),
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
            $this->createClassLoader(),
            $this->createClassNodeFinder(),
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
            $this->createClassLoader(),
            $this->createClassNodeFinder(),
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
    public function createCopyModuleFileManifestStrategy(): ManifestStrategyInterface
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
            $this->createConstantConfigurationEnvironmentStrategy(),
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
     * @return \SprykerSdk\Integrator\Builder\ConfigurationEnvironmentBuilder\ConstantConfigurationEnvironmentStrategy
     */
    public function createConstantConfigurationEnvironmentStrategy(): ConstantConfigurationEnvironmentStrategy
    {
        return new ConstantConfigurationEnvironmentStrategy();
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
    public function createWireNavigationManifestStrategy(): ManifestStrategyInterface
    {
        return new WireNavigationManifestStrategy(
            $this->getConfig(),
            $this->createClassHelper(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\ManifestStrategy\ManifestStrategyInterface
     */
    public function createUnwireNavigationManifestStrategy(): ManifestStrategyInterface
    {
        return new UnwireNavigationManifestStrategy(
            $this->getConfig(),
            $this->createClassHelper(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\ManifestStrategy\ManifestStrategyInterface
     */
    public function createWireTransferManifestStrategy(): ManifestStrategyInterface
    {
        return new WireTransferManifestStrategy(
            $this->getConfig(),
            $this->createFilesystem(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\ManifestStrategy\ManifestStrategyInterface
     */
    public function createWireSchemaManifestStrategy(): ManifestStrategyInterface
    {
        return new WireSchemaManifestStrategy(
            $this->getConfig(),
            $this->createFilesystem(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassMetadataBuilder\ClassMetadataBuilderInterface
     */
    public function createClassMetadataBuilder(): ClassMetadataBuilderInterface
    {
        return new ClassMetadataBuilder();
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassWriter\ClassFileWriterInterface
     */
    public function createClassFileWriter(): ClassFileWriterInterface
    {
        return new ClassFileWriter($this->createClassPrinter(), $this->createFileStorage());
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\FileNormalizer\FileNormalizersExecutorInterface
     */
    public function createFileNormalizersExecutor(): FileNormalizersExecutorInterface
    {
        return new FileNormalizersExecutor(
            $this->createFileStorage(),
            [
                $this->createCodeSnifferCompositeNormalizer(),
            ],
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\FileNormalizer\FileNormalizerInterface
     */
    public function createCodeSnifferCompositeNormalizer(): FileNormalizerInterface
    {
        return new CodeSnifferCompositeNormalizer(
            [
                $this->createPhpCSFixerNormalizer(),
                $this->createCodeSniffStyleFileNormalizer(),
            ],
            $this->createComposerLockReader(),
            $this->getConfig(),
            $this->createFilesystem(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\FileNormalizer\FileNormalizerInterface
     */
    public function createCodeSniffStyleFileNormalizer(): FileNormalizerInterface
    {
        return new CodeSniffStyleFileNormalizer($this->getConfig(), $this->createProcessRunnerService());
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\FileNormalizer\FileNormalizerInterface
     */
    public function createPhpCSFixerNormalizer(): FileNormalizerInterface
    {
        return new PhpCSFixerFileNormalizer($this->getConfig(), $this->createProcessRunnerService());
    }

    /**
     * @return \SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface
     */
    public function createProcessRunnerService(): ProcessRunnerServiceInterface
    {
        return new ProcessRunnerService();
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
     * @return \SprykerSdk\Integrator\Builder\FileStorage\FileStorageInterface
     */
    protected function createFileStorage(): FileStorageInterface
    {
        return (FileStorageFactory::getInstance())->createEmptyFilesStorage();
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassResolver\ClassResolverInterface
     */
    public function createClassResolver(): ClassResolverInterface
    {
        return new ClassResolver($this->createClassLoader(), $this->createClassGenerator());
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\Resolver\PrefixedConstNameResolverInterface
     */
    protected function createPrefixedConstNameResolver(): PrefixedConstNameResolverInterface
    {
        return new PrefixedConstNameResolver($this->createClassConstantFinder(), $this->createClassLoader());
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
            $this->createBuilderFactory(),
            $this->getConfig(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstance\Wire\WireClassInstanceModifierInterface
     */
    public function createWireClassInstanceModifier(): WireClassInstanceModifierInterface
    {
        return new WireClassInstanceModifier(
            $this->createCommonClassModifier(),
            $this->createClassNodeFinder(),
            $this->createClassMethodChecker(),
            $this->getWireModifierStrategies(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstance\Unwire\UnwireClassInstanceModifierInterface
     */
    public function createUnwireClassInstanceModifier(): UnwireClassInstanceModifierInterface
    {
        return new UnwireClassInstanceModifier(
            $this->createCommonClassModifier(),
            $this->createClassNodeFinder(),
            $this->createClassMethodChecker(),
            $this->getUnwireModifierStrategies(),
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
            $this->createMethodCreator(),
            $this->createMethodStatementsCreator(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\Creator\MethodCreatorInterface
     */
    public function createMethodCreator(): MethodCreatorInterface
    {
        return new MethodCreator(
            $this->createClassNodeFinder(),
            $this->createMethodStatementsCreator(),
            $this->createMethodDocBlockCreator(),
            $this->createMethodReturnTypeCreator(),
            $this->createParserFactory(),
            $this->createPrefixedConstNameResolver(),
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
    public function createMethodStatementsCreator(): MethodStatementsCreatorInterface
    {
        return new MethodStatementsCreator(
            $this->createPrefixedConstNameResolver(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassConstant\Wire\WireClassConstantModifierInterface
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
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassConstant\Unwire\UnwireClassConstantModifierInterface
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
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassConstant\ClassConstantModifierInterface
     */
    public function createClassConstantModifier(): ClassConstantModifierInterface
    {
        return new ClassConstantModifier(
            new ParserFactory(),
            $this->createClassConstantFinder(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\GlueRelationship\Wire\WireGlueRelationshipModifierInterface
     */
    public function createWireGlueRelationshipModifier(): WireGlueRelationshipModifierInterface
    {
        return new WireGlueRelationshipModifier(
            $this->getPhpParserNodeTraverser(),
            $this->createCommonClassModifier(),
            $this->createClassNodeFinder(),
            $this->createClassHelper(),
            $this->createBuilderFactory(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\GlueRelationship\Unwire\UnwireGlueRelationshipModifierInterface
     */
    public function createUnwireGlueRelationshipModifier(): UnwireGlueRelationshipModifierInterface
    {
        return new UnwireGlueRelationshipModifier(
            $this->getPhpParserNodeTraverser(),
            $this->createCommonClassModifier(),
            $this->createClassNodeFinder(),
            $this->createClassHelper(),
            $this->createBuilderFactory(),
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
    public function createBuilderFactory(): BuilderFactory
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
     * @return array<\SprykerSdk\Integrator\ManifestStrategy\ManifestStrategyInterface>
     */
    public function getManifestStrategies(): array
    {
        return [
            $this->createWirePluginManifestStrategy(),
            $this->createUnwirePluginManifestStrategy(),
            $this->createWireWidgetManifestStrategy(),
            $this->createUnwireWidgetManifestStrategy(),
            $this->createAddConfigArrayElementManifestStrategy(),
            $this->createConfigureModuleManifestStrategy(),
            $this->createCopyModuleFileManifestStrategy(),
            $this->createConfigureEnvManifestStrategy(),
            $this->createWireGlueRelationshipManifestStrategy(),
            $this->createUnwireGlueRelationshipManifestStrategy(),
            $this->createExecuteConsoleManifestStrategy(),
            $this->createWireNavigationManifestStrategy(),
            $this->createUnwireNavigationManifestStrategy(),
            $this->createGlossaryManifestStrategy(),
            $this->createWireTransferManifestStrategy(),
            $this->createWireSchemaManifestStrategy(),
        ];
    }

    /**
     * @return array<\SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Wire\WireClassInstanceModifierStrategyInterface>
     */
    public function getWireModifierStrategies(): array
    {
        return [
            $this->createReturnArrayWireClassInstanceModifierStrategy(),
            $this->createReturnClassWireClassInstanceModifierStrategy(),
            $this->createReturnChainedCollectionWireClassInstanceModifierStrategy(),
            $this->createReturnCollectionWireClassInstanceModifierStrategy(),
            $this->createReturnExtendContainerWireClassInstanceModifierStrategy(),
        ];
    }

    /**
     * @return array<\SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Unwire\UnwireClassInstanceModifierStrategyInterface>
     */
    public function getUnwireModifierStrategies(): array
    {
        return [
            $this->createReturnArrayUnwireClassInstanceModifierStrategy(),
            $this->createReturnClassUnwireClassInstanceModifierStrategy(),
            $this->createReturnChainedCollectionUnwireClassInstanceModifierStrategy(),
            $this->createReturnCollectionUnwireClassInstanceModifierStrategy(),
            $this->createReturnExtendContainerUnwireClassInstanceModifierStrategy(),
        ];
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Wire\WireClassInstanceModifierStrategyInterface
     */
    public function createReturnArrayWireClassInstanceModifierStrategy(): WireClassInstanceModifierStrategyInterface
    {
        return new ReturnArrayWireClassInstanceModifierStrategy(
            $this->createReturnArrayModifierApplicableModifierStrategy(),
            $this->createPluginPositionResolver(),
            $this->createNodeExpressionPartialParser(),
            $this->createArgumentBuilder(),
            $this->createClassLoader(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Wire\WireClassInstanceModifierStrategyInterface
     */
    public function createReturnCollectionWireClassInstanceModifierStrategy(): WireClassInstanceModifierStrategyInterface
    {
        return new ReturnCollectionWireClassInstanceModifierStrategy(
            $this->createArgumentBuilder(),
            $this->createReturnCollectionApplicableModifierStrategy(),
            $this->createPluginPositionResolver(),
            $this->createClassLoader(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Wire\WireClassInstanceModifierStrategyInterface
     */
    public function createReturnExtendContainerWireClassInstanceModifierStrategy(): WireClassInstanceModifierStrategyInterface
    {
        return new ReturnExtendContainerWireClassInstanceModifierStrategy(
            $this->createArgumentBuilder(),
            $this->createReturnExtendContainerApplicableModifierStrategy(),
            $this->createPluginPositionResolver(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Wire\WireClassInstanceModifierStrategyInterface
     */
    public function createReturnChainedCollectionWireClassInstanceModifierStrategy(): WireClassInstanceModifierStrategyInterface
    {
        return new ReturnChainedCollectionWireClassInstanceModifierStrategy(
            $this->createArgumentBuilder(),
            $this->createReturnChainedCollectionApplicableModifierStrategy(),
            $this->createPluginPositionResolver(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Wire\WireClassInstanceModifierStrategyInterface
     */
    public function createReturnClassWireClassInstanceModifierStrategy(): WireClassInstanceModifierStrategyInterface
    {
        return new ReturnClassWireClassInstanceModifierStrategy(
            $this->createCommonClassModifier(),
            $this->createMethodCreator(),
            $this->createClassNodeFinder(),
            $this->createReturnClassModifierApplicableModifierStrategy(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Unwire\UnwireClassInstanceModifierStrategyInterface
     */
    public function createReturnArrayUnwireClassInstanceModifierStrategy(): UnwireClassInstanceModifierStrategyInterface
    {
        return new ReturnArrayUnwireClassInstanceModifierStrategy(
            $this->createReturnArrayModifierApplicableModifierStrategy(),
            $this->createArgumentBuilder(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Unwire\UnwireClassInstanceModifierStrategyInterface
     */
    public function createReturnCollectionUnwireClassInstanceModifierStrategy(): UnwireClassInstanceModifierStrategyInterface
    {
        return new ReturnCollectionUnwireClassInstanceModifierStrategy(
            $this->createReturnCollectionApplicableModifierStrategy(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Unwire\UnwireClassInstanceModifierStrategyInterface
     */
    public function createReturnExtendContainerUnwireClassInstanceModifierStrategy(): UnwireClassInstanceModifierStrategyInterface
    {
        return new ReturnExtendContainerUnwireClassInstanceModifierStrategy(
            $this->createReturnExtendContainerApplicableModifierStrategy(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Unwire\UnwireClassInstanceModifierStrategyInterface
     */
    public function createReturnChainedCollectionUnwireClassInstanceModifierStrategy(): UnwireClassInstanceModifierStrategyInterface
    {
        return new ReturnChainedCollectionUnwireClassInstanceModifierStrategy(
            $this->createReturnChainedCollectionApplicableModifierStrategy(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Unwire\UnwireClassInstanceModifierStrategyInterface
     */
    public function createReturnClassUnwireClassInstanceModifierStrategy(): UnwireClassInstanceModifierStrategyInterface
    {
        return new ReturnClassUnwireClassInstanceModifierStrategy(
            $this->createCommonClassModifier(),
            $this->createReturnClassModifierApplicableModifierStrategy(),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Applicable\ReturnArrayModifierApplicableModifierStrategy
     */
    public function createReturnArrayModifierApplicableModifierStrategy(): ApplicableModifierStrategyInterface
    {
        return new ReturnArrayModifierApplicableModifierStrategy();
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Applicable\ReturnCollectionApplicableModifierStrategy
     */
    public function createReturnCollectionApplicableModifierStrategy(): ApplicableModifierStrategyInterface
    {
        return new ReturnCollectionApplicableModifierStrategy();
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Applicable\ReturnExtendContainerApplicableModifierStrategy
     */
    public function createReturnExtendContainerApplicableModifierStrategy(): ApplicableModifierStrategyInterface
    {
        return new ReturnExtendContainerApplicableModifierStrategy();
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Applicable\ReturnChainedCollectionApplicableModifierStrategy
     */
    public function createReturnChainedCollectionApplicableModifierStrategy(): ApplicableModifierStrategyInterface
    {
        return new ReturnChainedCollectionApplicableModifierStrategy();
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Applicable\ReturnClassModifierApplicableModifierStrategy
     */
    public function createReturnClassModifierApplicableModifierStrategy(): ApplicableModifierStrategyInterface
    {
        return new ReturnClassModifierApplicableModifierStrategy();
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\ArgumentBuilder\ArgumentBuilderInterface
     */
    public function createArgumentBuilder(): ArgumentBuilderInterface
    {
        return new ArgumentBuilder($this->createBuilderFactory(), $this->createNodeExpressionPartialParser());
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\PartialParser\ExpressionPartialParserInterface
     */
    public function createNodeExpressionPartialParser(): ExpressionPartialParserInterface
    {
        return new ExpressionPartialParser(
            (new ParserFactory())->create(ParserFactory::PREFER_PHP7),
        );
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\Visitor\PluginPositionResolver\PluginPositionResolverInterface
     */
    protected function createPluginPositionResolver(): PluginPositionResolverInterface
    {
        return new PluginPositionResolver();
    }

    /**
     * @return \SprykerSdk\Integrator\Filter\ManifestsFiltersExecutorInterface
     */
    protected function createManifestsFiltersExecutor(): ManifestsFiltersExecutorInterface
    {
        return new ManifestsFiltersExecutor([
            new RatingBasedManifestsFilter(
                $this->createConfigurationProvider(),
                $this->createModuleRatingFetcher(),
                $this->createManifestToModulesRatingRequestMapper(),
            ),
        ]);
    }

    /**
     * @return \SprykerSdk\Integrator\Communication\ReleaseApp\ModuleRatingFetcherInterface
     */
    protected function createModuleRatingFetcher(): ModuleRatingFetcherInterface
    {
        return new ModuleRatingFetcher(new Client(), $this->createConfigurationProvider(), new ModuleRatingResponseMapper());
    }

    /**
     * @return \SprykerSdk\Integrator\Configuration\ConfigurationProviderInterface
     */
    protected function createConfigurationProvider(): ConfigurationProviderInterface
    {
        return new ConfigurationProvider();
    }

    /**
     * @return \SprykerSdk\Integrator\Filter\RatingBasedManifestFilter\ManifestToModulesRatingRequestMapper
     */
    protected function createManifestToModulesRatingRequestMapper(): ManifestToModulesRatingRequestMapper
    {
        return new ManifestToModulesRatingRequestMapper();
    }

    /**
     * @return \Symfony\Component\Filesystem\Filesystem
     */
    protected function createFilesystem(): Filesystem
    {
        return new Filesystem();
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\Finder\ClassConstantFinderInterface
     */
    protected function createClassConstantFinder(): ClassConstantFinderInterface
    {
        return new ClassConstantFinder($this->createClassNodeFinder());
    }
}
