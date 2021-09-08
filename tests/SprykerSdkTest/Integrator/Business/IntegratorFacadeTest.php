<?php

namespace SprykerSdkTest\Integrator\Business;

use SprykerSdk\Integrator\Business\IntegratorFacade;
use SprykerSdk\Integrator\Dependency\Console\SymfonyConsoleInputOutputAdapter;
use SprykerSdk\Shared\Transfer\ModuleFilterTransfer;
use SprykerSdkTest\Integrator\BaseTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

class IntegratorFacadeTest extends BaseTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
    }

    public function testRunInstallation()
    {
        // before test
        $this->removeTmpDirectory();

        $this->createTmpDirectory();
        $this->copyProjectToTmpDirctory();



        // $inputOption = new InputOption(static::INPUT_OPTION_MODULE);


        $inputDefinition = new InputDefinition();
//        $inputDefinition->addOption($inputOption);
//        $inputDefinition->addOption($levelOption);
//        $inputDefinition->addOption($dryOption);
//        $inputDefinition->addOption($verboseOption);

        $input = new ArrayInput([], $inputDefinition);

        $output = new BufferedOutput();
        $io = new SymfonyStyle($input, $output);

        $this->createIntegratorFacade()->runInstallation($this->getModuleList(), new SymfonyConsoleInputOutputAdapter($io),false);

        $this->assertEquals(true, true);
    }


    private function getModuleList()
    {
        // TODO remove builder
        return $this->getFactory()->getModuleFinderFacade()->getModules($this->buildModuleFilterTransfer());
    }

    private function buildModuleFilterTransfer()
    {
        return new ModuleFilterTransfer();
    }

    /**
     * @return \SprykerSdk\Integrator\Business\IntegratorFacade
     */
    private function createIntegratorFacade(): IntegratorFacade
    {
        return new IntegratorFacade();
    }

    private function createTmpDirectory(): void
    {
        if (!$this->createFilesystem()->exists('tests/tmp')) {
             $this->createFilesystem()->mkdir('tests/tmp', 0700);
        }
    }

    private function removeTmpDirectory(): bool
    {
        $fileSystem = $this->createFilesystem();

        if ($fileSystem->exists('tests/tmp')) {
            $fileSystem->remove('tests/tmp');
        }

        return false;
    }

    private function copyProjectToTmpDirctory(): void
    {
        $fileSystem = $this->createFilesystem();

        if ($fileSystem->exists('tests/tmp')) {
            $fileSystem->mirror('tests/project/', 'tests/tmp/');
        }
    }

}
