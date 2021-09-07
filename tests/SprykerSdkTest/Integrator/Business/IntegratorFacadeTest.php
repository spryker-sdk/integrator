<?php

namespace SprykerSdkTest\Integrator\Business;


use SprykerSdk\Integrator\Business\IntegratorFacade;
use SprykerSdkTest\Integrator\BaseTestCase;

class IntegratorFacadeTest extends BaseTestCase
{
    public function testRunInstallation()
    {

        $this->createIntegratorFacade()->runInstallation();

        $this->assertEquals(true, true);
    }

    /**
     * @return \SprykerSdk\Integrator\Business\IntegratorFacade
     */
    private function createIntegratorFacade(): IntegratorFacade
    {
        return new IntegratorFacade();
    }

}
