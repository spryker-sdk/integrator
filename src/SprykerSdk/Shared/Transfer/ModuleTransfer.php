<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Shared\Transfer;

use ArrayObject;
use InvalidArgumentException;

class ModuleTransfer extends AbstractTransfer
{
    public const NAME = 'name';

    public const NAME_DASHED = 'nameDashed';

    public const ORGANIZATION = 'organization';

    public const APPLICATION = 'application';

    public const LAYER = 'layer';

    public const PATH = 'path';

    public const OPTIONS = 'options';

    public const DEPENDENT_MODULE = 'dependentModule';

    public const APPLICATIONS = 'applications';

    public const IS_STANDALONE = 'isStandalone';

    /**
     * @var string|null
     */
    protected $name;

    /**
     * @var string|null
     */
    protected $nameDashed;

    /**
     * @var \SprykerSdk\Shared\Transfer\OrganizationTransfer|null
     */
    protected $organization;

    /**
     * @var \SprykerSdk\Shared\Transfer\ApplicationTransfer|null
     */
    protected $application;

    /**
     * @var \SprykerSdk\Shared\Transfer\LayerTransfer|null
     */
    protected $layer;

    /**
     * @var string|null
     */
    protected $path;

    /**
     * @var \SprykerSdk\Shared\Transfer\OptionsTransfer|null
     */
    protected $options;

    /**
     * @var \SprykerSdk\Shared\Transfer\ModuleTransfer|null
     */
    protected $dependentModule;

    /**
     * @var \ArrayObject|\SprykerSdk\Shared\Transfer\ApplicationTransfer[]
     */
    protected $applications;

    /**
     * @var bool|null
     */
    protected $isStandalone;

    /**
     * @var array
     */
    protected $transferPropertyNameMap = [
        'name' => 'name',
        'Name' => 'name',
        'name_dashed' => 'nameDashed',
        'nameDashed' => 'nameDashed',
        'NameDashed' => 'nameDashed',
        'organization' => 'organization',
        'Organization' => 'organization',
        'application' => 'application',
        'Application' => 'application',
        'layer' => 'layer',
        'Layer' => 'layer',
        'path' => 'path',
        'Path' => 'path',
        'options' => 'options',
        'Options' => 'options',
        'dependent_module' => 'dependentModule',
        'dependentModule' => 'dependentModule',
        'DependentModule' => 'dependentModule',
        'applications' => 'applications',
        'Applications' => 'applications',
        'is_standalone' => 'isStandalone',
        'isStandalone' => 'isStandalone',
        'IsStandalone' => 'isStandalone',
    ];

    /**
     * @var array
     */
    protected $transferMetadata = [
        self::NAME => [
            'type' => 'string',
            'type_shim' => null,
            'name_underscore' => 'name',
            'is_collection' => false,
            'is_transfer' => false,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => false,
            'is_strict' => false,
        ],
        self::NAME_DASHED => [
            'type' => 'string',
            'type_shim' => null,
            'name_underscore' => 'name_dashed',
            'is_collection' => false,
            'is_transfer' => false,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => false,
            'is_strict' => false,
        ],
        self::ORGANIZATION => [
            'type' => 'Shared\Transfer\OrganizationTransfer',
            'type_shim' => null,
            'name_underscore' => 'organization',
            'is_collection' => false,
            'is_transfer' => true,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => false,
            'is_strict' => false,
        ],
        self::APPLICATION => [
            'type' => 'Shared\Transfer\ApplicationTransfer',
            'type_shim' => null,
            'name_underscore' => 'application',
            'is_collection' => false,
            'is_transfer' => true,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => false,
            'is_strict' => false,
        ],
        self::LAYER => [
            'type' => 'Shared\Transfer\LayerTransfer',
            'type_shim' => null,
            'name_underscore' => 'layer',
            'is_collection' => false,
            'is_transfer' => true,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => false,
            'is_strict' => false,
        ],
        self::PATH => [
            'type' => 'string',
            'type_shim' => null,
            'name_underscore' => 'path',
            'is_collection' => false,
            'is_transfer' => false,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => false,
            'is_strict' => false,
        ],
        self::OPTIONS => [
            'type' => 'Shared\Transfer\OptionsTransfer',
            'type_shim' => null,
            'name_underscore' => 'options',
            'is_collection' => false,
            'is_transfer' => true,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => false,
            'is_strict' => false,
        ],
        self::DEPENDENT_MODULE => [
            'type' => 'Shared\Transfer\ModuleTransfer',
            'type_shim' => null,
            'name_underscore' => 'dependent_module',
            'is_collection' => false,
            'is_transfer' => true,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => false,
            'is_strict' => false,
        ],
        self::APPLICATIONS => [
            'type' => 'Shared\Transfer\ApplicationTransfer',
            'type_shim' => null,
            'name_underscore' => 'applications',
            'is_collection' => true,
            'is_transfer' => true,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => false,
            'is_strict' => false,
        ],
        self::IS_STANDALONE => [
            'type' => 'bool',
            'type_shim' => null,
            'name_underscore' => 'is_standalone',
            'is_collection' => false,
            'is_transfer' => false,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => false,
            'is_strict' => false,
        ],
    ];

    /**
     * @module Integrator|SprykGui|Development|ModuleFinder
     *
     * @param string|null $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        $this->modifiedProperties[self::NAME] = true;

        return $this;
    }

    /**
     * @module Integrator|SprykGui|Development|ModuleFinder
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @module Integrator|SprykGui|Development|ModuleFinder
     *
     * @return string
     */
    public function getNameOrFail()
    {
        if ($this->name === null) {
            $this->throwNullValueException(static::NAME);
        }

        return $this->name;
    }

    /**
     * @module Integrator|SprykGui|Development|ModuleFinder
     *
     * @return $this
     */
    public function requireName()
    {
        $this->assertPropertyIsSet(self::NAME);

        return $this;
    }

    /**
     * @module Integrator|Development|ModuleFinder
     *
     * @param string|null $nameDashed
     *
     * @return $this
     */
    public function setNameDashed($nameDashed)
    {
        $this->nameDashed = $nameDashed;
        $this->modifiedProperties[self::NAME_DASHED] = true;

        return $this;
    }

    /**
     * @module Integrator|Development|ModuleFinder
     *
     * @return string|null
     */
    public function getNameDashed()
    {
        return $this->nameDashed;
    }

    /**
     * @module Integrator|Development|ModuleFinder
     *
     * @return string
     */
    public function getNameDashedOrFail()
    {
        if ($this->nameDashed === null) {
            $this->throwNullValueException(static::NAME_DASHED);
        }

        return $this->nameDashed;
    }

    /**
     * @module Integrator|Development|ModuleFinder
     *
     * @return $this
     */
    public function requireNameDashed()
    {
        $this->assertPropertyIsSet(self::NAME_DASHED);

        return $this;
    }

    /**
     * @module Integrator|SprykGui|Development|ModuleFinder
     *
     * @param \SprykerSdk\Shared\Transfer\OrganizationTransfer|null $organization
     *
     * @return $this
     */
    public function setOrganization(?OrganizationTransfer $organization = null)
    {
        $this->organization = $organization;
        $this->modifiedProperties[self::ORGANIZATION] = true;

        return $this;
    }

    /**
     * @module Integrator|SprykGui|Development|ModuleFinder
     *
     * @return \SprykerSdk\Shared\Transfer\OrganizationTransfer|null
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @module Integrator|SprykGui|Development|ModuleFinder
     *
     * @return \SprykerSdk\Shared\Transfer\OrganizationTransfer
     */
    public function getOrganizationOrFail()
    {
        if ($this->organization === null) {
            $this->throwNullValueException(static::ORGANIZATION);
        }

        return $this->organization;
    }

    /**
     * @module Integrator|SprykGui|Development|ModuleFinder
     *
     * @return $this
     */
    public function requireOrganization()
    {
        $this->assertPropertyIsSet(self::ORGANIZATION);

        return $this;
    }

    /**
     * @module SprykGui|Development|ModuleFinder
     *
     * @param \SprykerSdk\Shared\Transfer\ApplicationTransfer|null $application
     *
     * @return $this
     */
    public function setApplication(?ApplicationTransfer $application = null)
    {
        $this->application = $application;
        $this->modifiedProperties[self::APPLICATION] = true;

        return $this;
    }

    /**
     * @module SprykGui|Development|ModuleFinder
     *
     * @return \SprykerSdk\Shared\Transfer\ApplicationTransfer|null
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @module SprykGui|Development|ModuleFinder
     *
     * @return \SprykerSdk\Shared\Transfer\ApplicationTransfer
     */
    public function getApplicationOrFail()
    {
        if ($this->application === null) {
            $this->throwNullValueException(static::APPLICATION);
        }

        return $this->application;
    }

    /**
     * @module SprykGui|Development|ModuleFinder
     *
     * @return $this
     */
    public function requireApplication()
    {
        $this->assertPropertyIsSet(self::APPLICATION);

        return $this;
    }

    /**
     * @module SprykGui
     *
     * @param \SprykerSdk\Shared\Transfer\LayerTransfer|null $layer
     *
     * @return $this
     */
    public function setLayer(?LayerTransfer $layer = null)
    {
        $this->layer = $layer;
        $this->modifiedProperties[self::LAYER] = true;

        return $this;
    }

    /**
     * @module SprykGui
     *
     * @return \SprykerSdk\Shared\Transfer\LayerTransfer|null
     */
    public function getLayer()
    {
        return $this->layer;
    }

    /**
     * @module SprykGui
     *
     * @return \SprykerSdk\Shared\Transfer\LayerTransfer
     */
    public function getLayerOrFail()
    {
        if ($this->layer === null) {
            $this->throwNullValueException(static::LAYER);
        }

        return $this->layer;
    }

    /**
     * @module SprykGui
     *
     * @return $this
     */
    public function requireLayer()
    {
        $this->assertPropertyIsSet(self::LAYER);

        return $this;
    }

    /**
     * @module SprykGui|Development|ModuleFinder
     *
     * @param string|null $path
     *
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;
        $this->modifiedProperties[self::PATH] = true;

        return $this;
    }

    /**
     * @module SprykGui|Development|ModuleFinder
     *
     * @return string|null
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @module SprykGui|Development|ModuleFinder
     *
     * @return string
     */
    public function getPathOrFail()
    {
        if ($this->path === null) {
            $this->throwNullValueException(static::PATH);
        }

        return $this->path;
    }

    /**
     * @module SprykGui|Development|ModuleFinder
     *
     * @return $this
     */
    public function requirePath()
    {
        $this->assertPropertyIsSet(self::PATH);

        return $this;
    }

    /**
     * @module SprykGui
     *
     * @param \SprykerSdk\Shared\Transfer\OptionsTransfer|null $options
     *
     * @return $this
     */
    public function setOptions(?OptionsTransfer $options = null)
    {
        $this->options = $options;
        $this->modifiedProperties[self::OPTIONS] = true;

        return $this;
    }

    /**
     * @module SprykGui
     *
     * @return \SprykerSdk\Shared\Transfer\OptionsTransfer|null
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @module SprykGui
     *
     * @return \SprykerSdk\Shared\Transfer\OptionsTransfer
     */
    public function getOptionsOrFail()
    {
        if ($this->options === null) {
            $this->throwNullValueException(static::OPTIONS);
        }

        return $this->options;
    }

    /**
     * @module SprykGui
     *
     * @return $this
     */
    public function requireOptions()
    {
        $this->assertPropertyIsSet(self::OPTIONS);

        return $this;
    }

    /**
     * @module SprykGui
     *
     * @param \SprykerSdk\Shared\Transfer\ModuleTransfer|null $dependentModule
     *
     * @return $this
     */
    public function setDependentModule(?ModuleTransfer $dependentModule = null)
    {
        $this->dependentModule = $dependentModule;
        $this->modifiedProperties[self::DEPENDENT_MODULE] = true;

        return $this;
    }

    /**
     * @module SprykGui
     *
     * @return \SprykerSdk\Shared\Transfer\ModuleTransfer|null
     */
    public function getDependentModule()
    {
        return $this->dependentModule;
    }

    /**
     * @module SprykGui
     *
     * @return \SprykerSdk\Shared\Transfer\ModuleTransfer
     */
    public function getDependentModuleOrFail()
    {
        if ($this->dependentModule === null) {
            $this->throwNullValueException(static::DEPENDENT_MODULE);
        }

        return $this->dependentModule;
    }

    /**
     * @module SprykGui
     *
     * @return $this
     */
    public function requireDependentModule()
    {
        $this->assertPropertyIsSet(self::DEPENDENT_MODULE);

        return $this;
    }

    /**
     * @module Development|ModuleFinder
     *
     * @param \ArrayObject|\SprykerSdk\Shared\Transfer\ApplicationTransfer[] $applications
     *
     * @return $this
     */
    public function setApplications(ArrayObject $applications)
    {
        $this->applications = $applications;
        $this->modifiedProperties[self::APPLICATIONS] = true;

        return $this;
    }

    /**
     * @module Development|ModuleFinder
     *
     * @return \ArrayObject|\SprykerSdk\Shared\Transfer\ApplicationTransfer[]
     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
     * @module Development|ModuleFinder
     *
     * @param \SprykerSdk\Shared\Transfer\ApplicationTransfer $application
     *
     * @return $this
     */
    public function addApplication(ApplicationTransfer $application)
    {
        $this->applications[] = $application;
        $this->modifiedProperties[self::APPLICATIONS] = true;

        return $this;
    }

    /**
     * @module Development|ModuleFinder
     *
     * @return $this
     */
    public function requireApplications()
    {
        $this->assertCollectionPropertyIsSet(self::APPLICATIONS);

        return $this;
    }

    /**
     * @module Development|ModuleFinder
     *
     * @param bool|null $isStandalone
     *
     * @return $this
     */
    public function setIsStandalone($isStandalone)
    {
        $this->isStandalone = $isStandalone;
        $this->modifiedProperties[self::IS_STANDALONE] = true;

        return $this;
    }

    /**
     * @module Development|ModuleFinder
     *
     * @return bool|null
     */
    public function getIsStandalone()
    {
        return $this->isStandalone;
    }

    /**
     * @module Development|ModuleFinder
     *
     * @return bool
     */
    public function getIsStandaloneOrFail()
    {
        if ($this->isStandalone === null) {
            $this->throwNullValueException(static::IS_STANDALONE);
        }

        return $this->isStandalone;
    }

    /**
     * @module Development|ModuleFinder
     *
     * @return $this
     */
    public function requireIsStandalone()
    {
        $this->assertPropertyIsSet(self::IS_STANDALONE);

        return $this;
    }

    /**
     * @param array $data
     * @param bool $ignoreMissingProperty
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function fromArray(array $data, $ignoreMissingProperty = false)
    {
        foreach ($data as $property => $value) {
            $normalizedPropertyName = $this->transferPropertyNameMap[$property] ?? null;

            switch ($normalizedPropertyName) {
                case 'name':
                case 'nameDashed':
                case 'path':
                case 'isStandalone':
                    $this->$normalizedPropertyName = $value;
                    $this->modifiedProperties[$normalizedPropertyName] = true;

                    break;
                case 'organization':
                case 'application':
                case 'layer':
                case 'options':
                case 'dependentModule':
                    if (is_array($value)) {
                        $type = $this->transferMetadata[$normalizedPropertyName]['type'];
                        $value = (new $type())->fromArray($value, $ignoreMissingProperty);
                    }

                    if ($this->isPropertyStrict($normalizedPropertyName)) {
                        $this->assertInstanceOfTransfer($normalizedPropertyName, $value);
                    }
                    $this->$normalizedPropertyName = $value;
                    $this->modifiedProperties[$normalizedPropertyName] = true;

                    break;
                case 'applications':
                    $elementType = $this->transferMetadata[$normalizedPropertyName]['type'];
                    $this->$normalizedPropertyName = $this->processArrayObject($elementType, $value, $ignoreMissingProperty);
                    $this->modifiedProperties[$normalizedPropertyName] = true;

                    break;
                default:
                    if (!$ignoreMissingProperty) {
                        throw new InvalidArgumentException(sprintf('Missing property `%s` in `%s`', $property, static::class));
                    }
            }
        }

        return $this;
    }

    /**
     * @param bool $isRecursive
     * @param bool $camelCasedKeys
     *
     * @return array
     */
    public function modifiedToArray($isRecursive = true, $camelCasedKeys = false)
    {
        if ($isRecursive && !$camelCasedKeys) {
            return $this->modifiedToArrayRecursiveNotCamelCased();
        }
        if ($isRecursive && $camelCasedKeys) {
            return $this->modifiedToArrayRecursiveCamelCased();
        }
        if (!$isRecursive && $camelCasedKeys) {
            return $this->modifiedToArrayNotRecursiveCamelCased();
        }
        if (!$isRecursive && !$camelCasedKeys) {
            return $this->modifiedToArrayNotRecursiveNotCamelCased();
        }
    }

    /**
     * @param bool $isRecursive
     * @param bool $camelCasedKeys
     *
     * @return array
     */
    public function toArray($isRecursive = true, $camelCasedKeys = false)
    {
        if ($isRecursive && !$camelCasedKeys) {
            return $this->toArrayRecursiveNotCamelCased();
        }
        if ($isRecursive && $camelCasedKeys) {
            return $this->toArrayRecursiveCamelCased();
        }
        if (!$isRecursive && !$camelCasedKeys) {
            return $this->toArrayNotRecursiveNotCamelCased();
        }
        if (!$isRecursive && $camelCasedKeys) {
            return $this->toArrayNotRecursiveCamelCased();
        }
    }

    /**
     * @param mixed $value
     * @param bool $isRecursive
     * @param bool $camelCasedKeys
     *
     * @return array
     */
    protected function addValuesToCollectionModified($value, $isRecursive, $camelCasedKeys)
    {
        $result = [];
        foreach ($value as $elementKey => $arrayElement) {
            if ($arrayElement instanceof AbstractTransfer) {
                $result[$elementKey] = $arrayElement->modifiedToArray($isRecursive, $camelCasedKeys);

                continue;
            }
            $result[$elementKey] = $arrayElement;
        }

        return $result;
    }

    /**
     * @param mixed $value
     * @param bool $isRecursive
     * @param bool $camelCasedKeys
     *
     * @return array
     */
    protected function addValuesToCollection($value, $isRecursive, $camelCasedKeys)
    {
        $result = [];
        foreach ($value as $elementKey => $arrayElement) {
            if ($arrayElement instanceof AbstractTransfer) {
                $result[$elementKey] = $arrayElement->toArray($isRecursive, $camelCasedKeys);

                continue;
            }
            $result[$elementKey] = $arrayElement;
        }

        return $result;
    }

    /**
     * @return array
     */
    public function modifiedToArrayRecursiveCamelCased()
    {
        $values = [];
        foreach ($this->modifiedProperties as $property => $_) {
            $value = $this->$property;

            $arrayKey = $property;

            if ($value instanceof AbstractTransfer) {
                $values[$arrayKey] = $value->modifiedToArray(true, true);

                continue;
            }
            switch ($property) {
                case 'name':
                case 'nameDashed':
                case 'path':
                case 'isStandalone':
                    $values[$arrayKey] = $value;

                    break;
                case 'organization':
                case 'application':
                case 'layer':
                case 'options':
                case 'dependentModule':
                    $values[$arrayKey] = $value instanceof AbstractTransfer ? $value->modifiedToArray(true, true) : $value;

                    break;
                case 'applications':
                    $values[$arrayKey] = $value ? $this->addValuesToCollectionModified($value, true, true) : $value;

                    break;
            }
        }

        return $values;
    }

    /**
     * @return array
     */
    public function modifiedToArrayRecursiveNotCamelCased()
    {
        $values = [];
        foreach ($this->modifiedProperties as $property => $_) {
            $value = $this->$property;

            $arrayKey = $this->transferMetadata[$property]['name_underscore'];

            if ($value instanceof AbstractTransfer) {
                $values[$arrayKey] = $value->modifiedToArray(true, false);

                continue;
            }
            switch ($property) {
                case 'name':
                case 'nameDashed':
                case 'path':
                case 'isStandalone':
                    $values[$arrayKey] = $value;

                    break;
                case 'organization':
                case 'application':
                case 'layer':
                case 'options':
                case 'dependentModule':
                    $values[$arrayKey] = $value instanceof AbstractTransfer ? $value->modifiedToArray(true, false) : $value;

                    break;
                case 'applications':
                    $values[$arrayKey] = $value ? $this->addValuesToCollectionModified($value, true, false) : $value;

                    break;
            }
        }

        return $values;
    }

    /**
     * @return array
     */
    public function modifiedToArrayNotRecursiveNotCamelCased()
    {
        $values = [];
        foreach ($this->modifiedProperties as $property => $_) {
            $value = $this->$property;

            $arrayKey = $this->transferMetadata[$property]['name_underscore'];

            $values[$arrayKey] = $value;
        }

        return $values;
    }

    /**
     * @return array
     */
    public function modifiedToArrayNotRecursiveCamelCased()
    {
        $values = [];
        foreach ($this->modifiedProperties as $property => $_) {
            $value = $this->$property;

            $arrayKey = $property;

            $values[$arrayKey] = $value;
        }

        return $values;
    }

    /**
     * @return void
     */
    protected function initCollectionProperties()
    {
        $this->applications = $this->applications ?: new ArrayObject();
    }

    /**
     * @return array
     */
    public function toArrayNotRecursiveCamelCased()
    {
        return [
            'name' => $this->name,
            'nameDashed' => $this->nameDashed,
            'path' => $this->path,
            'isStandalone' => $this->isStandalone,
            'organization' => $this->organization,
            'application' => $this->application,
            'layer' => $this->layer,
            'options' => $this->options,
            'dependentModule' => $this->dependentModule,
            'applications' => $this->applications,
        ];
    }

    /**
     * @return array
     */
    public function toArrayNotRecursiveNotCamelCased()
    {
        return [
            'name' => $this->name,
            'name_dashed' => $this->nameDashed,
            'path' => $this->path,
            'is_standalone' => $this->isStandalone,
            'organization' => $this->organization,
            'application' => $this->application,
            'layer' => $this->layer,
            'options' => $this->options,
            'dependent_module' => $this->dependentModule,
            'applications' => $this->applications,
        ];
    }

    /**
     * @return array
     */
    public function toArrayRecursiveNotCamelCased()
    {
        return [
            'name' => $this->name instanceof AbstractTransfer ? $this->name->toArray(true, false) : $this->name,
            'name_dashed' => $this->nameDashed instanceof AbstractTransfer ? $this->nameDashed->toArray(true, false) : $this->nameDashed,
            'path' => $this->path instanceof AbstractTransfer ? $this->path->toArray(true, false) : $this->path,
            'is_standalone' => $this->isStandalone instanceof AbstractTransfer ? $this->isStandalone->toArray(true, false) : $this->isStandalone,
            'organization' => $this->organization instanceof AbstractTransfer ? $this->organization->toArray(true, false) : $this->organization,
            'application' => $this->application instanceof AbstractTransfer ? $this->application->toArray(true, false) : $this->application,
            'layer' => $this->layer instanceof AbstractTransfer ? $this->layer->toArray(true, false) : $this->layer,
            'options' => $this->options instanceof AbstractTransfer ? $this->options->toArray(true, false) : $this->options,
            'dependent_module' => $this->dependentModule instanceof AbstractTransfer ? $this->dependentModule->toArray(true, false) : $this->dependentModule,
            'applications' => $this->applications instanceof AbstractTransfer ? $this->applications->toArray(true, false) : $this->addValuesToCollection($this->applications, true, false),
        ];
    }

    /**
     * @return array
     */
    public function toArrayRecursiveCamelCased()
    {
        return [
            'name' => $this->name instanceof AbstractTransfer ? $this->name->toArray(true, true) : $this->name,
            'nameDashed' => $this->nameDashed instanceof AbstractTransfer ? $this->nameDashed->toArray(true, true) : $this->nameDashed,
            'path' => $this->path instanceof AbstractTransfer ? $this->path->toArray(true, true) : $this->path,
            'isStandalone' => $this->isStandalone instanceof AbstractTransfer ? $this->isStandalone->toArray(true, true) : $this->isStandalone,
            'organization' => $this->organization instanceof AbstractTransfer ? $this->organization->toArray(true, true) : $this->organization,
            'application' => $this->application instanceof AbstractTransfer ? $this->application->toArray(true, true) : $this->application,
            'layer' => $this->layer instanceof AbstractTransfer ? $this->layer->toArray(true, true) : $this->layer,
            'options' => $this->options instanceof AbstractTransfer ? $this->options->toArray(true, true) : $this->options,
            'dependentModule' => $this->dependentModule instanceof AbstractTransfer ? $this->dependentModule->toArray(true, true) : $this->dependentModule,
            'applications' => $this->applications instanceof AbstractTransfer ? $this->applications->toArray(true, true) : $this->addValuesToCollection($this->applications, true, true),
        ];
    }
}
