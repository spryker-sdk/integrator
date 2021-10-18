<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdkTest\Integrator\Helper;

use SprykerSdk\Integrator\Helper\ClassHelper;
use SprykerSdkTest\Integrator\BaseTestCase;

class ClassHelperTest extends BaseTestCase
{
    /**
     * @return void
     */
    public function testGetShortClassName(): void
    {
        $shortClassName = $this->createClassHelper()->getShortClassName(ClassHelper::class);
        $this->assertEquals('ClassHelper', $shortClassName);
    }

    /**
     * @return void
     */
    public function testGetShortClassNameOneLevel(): void
    {
        $shortClassName = $this->createClassHelper()->getShortClassName('\ClassHelper');
        $this->assertEquals('ClassHelper', $shortClassName);
    }

    /**
     * @return void
     */
    public function testGetShortClassNameByEmptyString(): void
    {
        $shortClassName = $this->createClassHelper()->getShortClassName('');
        $this->assertEquals('', $shortClassName);
    }

    /**
     * @return void
     */
    public function testGetClassNamespace(): void
    {
        $clsssNamespace = $this->createClassHelper()->getClassNamespace(ClassHelper::class);
        $this->assertEquals('SprykerSdk\Integrator\Helper', $clsssNamespace);
    }

    /**
     * @return void
     */
    public function testGetOrganisationName(): void
    {
        $organisationName = $this->createClassHelper()->getOrganisationName(ClassHelper::class);
        $this->assertEquals('SprykerSdk', $organisationName);
    }

    /**
     * @return void
     */
    public function testGetOrganisationNameByOneLevel(): void
    {
        $organisationName = $this->createClassHelper()->getOrganisationName('\ClassHelper');
        $this->assertEquals('ClassHelper', $organisationName);
    }

    /**
     * @return void
     */
    public function testGetOrganisationNameByEmptyClassName(): void
    {
        $organisationName = $this->createClassHelper()->getOrganisationName('');
        $this->assertEquals('', $organisationName);
    }

    /**
     * @return void
     */
    public function testGetModuleName(): void
    {
        $moduleName = $this->createClassHelper()->getModuleName(ClassHelper::class);
        $this->assertEquals('Helper', $moduleName);
    }

    /**
     * @return void
     */
    public function testGetModuleNameByOneLevelClassName(): void
    {
        $moduleName = $this->createClassHelper()->getModuleName('ClassHelper');
        $this->assertEquals('', $moduleName);
    }

    /**
     * @return void
     */
    public function testGetLayerName()
    {
        $layerName = $this->createClassHelper()->getLayerName(ClassHelper::class);
        $this->assertEquals('Integrator', $layerName);
    }

    /**
     * @return void
     */
    public function testGetLayerNameByOneLevelClassName()
    {
        $layerName = $this->createClassHelper()->getLayerName('ClassHelper');
        $this->assertEquals('', $layerName);
    }

    /**
     * @return void
     */
    public function testGetLayerNameByEmptyClassName()
    {
        $layerName = $this->createClassHelper()->getLayerName('');
        $this->assertEquals('', $layerName);
    }

    /**
     * @return \SprykerSdk\Integrator\Helper\ClassHelper
     */
    public function createClassHelper(): ClassHelper
    {
        return new ClassHelper();
    }
}
