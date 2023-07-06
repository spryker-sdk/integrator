<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator;

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\CloningVisitor;
use PhpParser\NodeVisitor\NameResolver;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\Dependency\Console\SymfonyConsoleInputOutputAdapter;
use SprykerSdk\Integrator\IntegratorConfig;
use SprykerSdk\Integrator\IntegratorFactoryAwareTrait;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;
use SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer;
use SprykerSdk\Integrator\Transfer\ModuleTransfer;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use ZipArchive;

abstract class BaseTestCase extends PHPUnitTestCase
{
    use IntegratorFactoryAwareTrait;

    /**
     * @return \Symfony\Component\Filesystem\Filesystem
     */
    protected function createFilesystem(): Filesystem
    {
        return new Filesystem();
    }

    /**
     * @return string
     */
    protected function getTempDirectoryPath(): string
    {
        return APPLICATION_ROOT_DIR;
    }

    /**
     * @return string
     */
    protected function getTempStandaloneModulesDirectoryPath(): string
    {
        return APPLICATION_STANDALONE_MODULES_DIR;
    }

    /**
     * @return string
     */
    protected function getDataDirectoryPath(): string
    {
        return DATA_PROVIDER_DIR;
    }

    /**
     * @return string
     */
    protected function getProjectMockOriginalPath(): string
    {
        return $this->getDataDirectoryPath() . DIRECTORY_SEPARATOR . 'integrator' . DIRECTORY_SEPARATOR . 'project_original';
    }

    /**
     * @return string
     */
    protected function getProjectMockCurrentPath(): string
    {
        return $this->getDataDirectoryPath() . DIRECTORY_SEPARATOR . 'integrator' . DIRECTORY_SEPARATOR . 'project_current';
    }

    /**
     * @return string
     */
    protected function getTestTmpDirPath(): string
    {
        return ROOT_TESTS . DIRECTORY_SEPARATOR . 'tmp';
    }

    /**
     * @param string $dirPath
     * @param string $zipPath
     *
     * @return void
     */
    protected static function zipDir(string $dirPath, string $zipPath): void
    {
        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        /** @var array<\SplFileInfo> $files */
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dirPath),
            RecursiveIteratorIterator::LEAVES_ONLY,
        );

        foreach ($files as $name => $file) {
            // Skip directories (they would be added automatically)
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strpos($filePath, 'Spryker/'));
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();
    }

    /**
     * @param bool $isDry
     * @param array<\SprykerSdk\Integrator\Transfer\ModuleTransfer> $ModuleTransfers
     *
     * @return \SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer
     */
    protected function createCommandArgumentsTransfer(bool $isDry = false, array $ModuleTransfers = []): IntegratorCommandArgumentsTransfer
    {
        $commandArgumentsTransfer = new IntegratorCommandArgumentsTransfer();
        $commandArgumentsTransfer->setModules($ModuleTransfers);
        $commandArgumentsTransfer->setIsDry($isDry);

        return $commandArgumentsTransfer;
    }

    /**
     * @param string $className
     * @param string $filePath
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    protected function createClassInformationTransfer(string $className, string $filePath): ClassInformationTransfer
    {
        $parser = $this->getFactory()->createPhpParserParser();

        $classInformationTransfer = (new ClassInformationTransfer())
            ->setClassName($className)
            ->setFullyQualifiedClassName($className);

        $originalSyntaxTree = $parser->parse(file_get_contents($filePath));
        $syntaxTree = $this->traverseOriginalSyntaxTree($originalSyntaxTree);

        $classInformationTransfer->setClassTokenTree($syntaxTree)
            ->setOriginalClassTokenTree($originalSyntaxTree);

        return $classInformationTransfer;
    }

    /**
     * @param array<\PhpParser\Node\Stmt>|null $originalSyntaxTree
     *
     * @return array<\PhpParser\Node>
     */
    protected function traverseOriginalSyntaxTree(?array $originalSyntaxTree): array
    {
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new CloningVisitor());
        $nodeTraverser->addVisitor(new NameResolver());

        return $nodeTraverser->traverse($originalSyntaxTree);
    }

    /**
     * @param string|null $moduleName
     *
     * @return \SprykerSdk\Integrator\Transfer\ModuleTransfer
     */
    protected function getModuleTransfer(?string $moduleName = null): ModuleTransfer
    {
        [$organization, $moduleName] = explode('.', $moduleName);

        return (new ModuleTransfer())
            ->setModule($moduleName)
            ->setOrganization($organization);
    }

    /**
     * @return \SprykerSdk\Integrator\Dependency\Console\SymfonyConsoleInputOutputAdapter
     */
    protected function buildSymfonyConsoleInputOutputAdapter(): SymfonyConsoleInputOutputAdapter
    {
        $io = new SymfonyStyle($this->buildInput(), $this->buildOutput());
        $ioAdapter = new SymfonyConsoleInputOutputAdapter($io);
        $ioAdapter->setNoIteration();

        return $ioAdapter;
    }

    /**
     * @return \Symfony\Component\Console\Input\InputInterface
     */
    protected function buildInput(): InputInterface
    {
        $verboseOption = new InputOption('verboseOption', null, InputOutputInterface::DEBUG);
        $inputDefinition = new InputDefinition();

        $inputDefinition->addOption($verboseOption);

        return new ArrayInput([], $inputDefinition);
    }

    /**
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    protected function buildOutput(): OutputInterface
    {
        return new BufferedOutput(OutputInterface::VERBOSITY_DEBUG);
    }
}
