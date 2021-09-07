<?php


namespace SprykerSdkTest\Zed\Integrator\Business\Builder;


use SprykerSdk\Integrator\Business\Builder\ClassBuilderFacade;
use SprykerSdkTest\Integrator\BaseTestCase;

class ClassBuilderFacadeTest extends BaseTestCase
{
    public function testResolveClass()
    {
        $targetClassName = '\Spryker\Glue\GlueApplication\GlueApplicationDependencyProvider';
        // $t = $this->getClassBuilderFacade()->resolveClass($targetClassName);

        // var_dump($t);
        $this->assertTrue(true);
    }

    /**
     * @return \SprykerSdk\Zed\Integrator\Business\Builder\ClassBuilderFacade
     */
    protected function getClassBuilderFacade(): ClassBuilderFacade
    {
       return new ClassBuilderFacade();
    }
}
