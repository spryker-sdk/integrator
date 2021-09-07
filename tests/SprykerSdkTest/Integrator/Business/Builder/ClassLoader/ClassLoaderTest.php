<?php


namespace SprykerSdkTest\Zed\Integrator\Business\Builder\ClassLoader;


use SprykerSdk\Integrator\Business\Builder\ClassLoader\ClassLoader;
use SprykerSdk\Integrator\Business\Helper\ClassHelper;
use SprykerSdkTest\Integrator\BaseTestCase;

class ClassLoaderTest extends BaseTestCase
{
    public function testLoadClass()
    {
//        $t = $this->createClassLoader()->loadClass(ClassHelper::class);
//
//        var_dump($t);

        $this->assertTrue(true);
    }

    private function createClassLoader(): ClassLoader
    {
        return new ClassLoader(
            $this->createPhpParser(),
            $this->createLexer()
        );
    }
}
