<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Transfer;

use ArrayObject;
use InvalidArgumentException;

class ModuleTransfer extends AbstractTransfer
{
    /**
     * @var string
     */
    public const NAME = 'name';

    /**
     * @var string
     */
    public const NAME_DASHED = 'nameDashed';

    /**
     * @var string
     */
    public const ORGANIZATION = 'organization';

    /**
     * @var string
     */
    public const APPLICATION = 'application';

    /**
     * @var string
     */
    public const LAYER = 'layer';

    /**
     * @var string
     */
    public const PATH = 'path';

    /**
     * @var string
     */
    public const OPTIONS = 'options';

    /**
     * @var string
     */
    public const DEPENDENT_MODULE = 'dependentModule';

    /**
     * @var string
     */
    public const APPLICATIONS = 'applications';

    /**
     * @var string
     */
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
     * @var \SprykerSdk\Integrator\Transfer\OrganizationTransfer|null
     */
    protected $organization;

    /**
     * @var \SprykerSdk\Integrator\Transfer\ApplicationTransfer|null
     */
    protected $application;

    /**
     * @var \SprykerSdk\Integrator\Transfer\AbstractTransfer|null
     */
    protected $layer;

    /**
     * @var string|null
     */
    protected $path;

    /**
     * @var \SprykerSdk\Integrator\Transfer\AbstractTransfer|null
     */
    protected $options;

    /**
     * @var \SprykerSdk\Integrator\Transfer\ModuleTransfer|null
     */
    protected $dependentModule;

    /**
     * @var array<\SprykerSdk\Integrator\Transfer\ApplicationTransfer>|\ArrayObject
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
        $this->modifiedProperties[static::NAME] = true;

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
        $this->assertPropertyIsSet(static::NAME);

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
        $this->modifiedProperties[static::NAME_DASHED] = true;

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
        $this->assertPropertyIsSet(static::NAME_DASHED);

        return $this;
    }

    /**
     * @module Integrator|SprykGui|Development|ModuleFinder
     *
     * @param \SprykerSdk\Integrator\Transfer\OrganizationTransfer|null $organization
     *
     * @return $this
     */
    public function setOrganization(?OrganizationTransfer $organization = null)
    {
        $this->organization = $organization;
        $this->modifiedProperties[static::ORGANIZATION] = true;

        return $this;
    }

    /**
     * @module Integrator|SprykGui|Development|ModuleFinder
     *
     * @return \SprykerSdk\Integrator\Transfer\OrganizationTransfer|null
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @module Integrator|SprykGui|Development|ModuleFinder
     *
     * @return \SprykerSdk\Integrator\Transfer\OrganizationTransfer
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
        $this->assertPropertyIsSet(static::ORGANIZATION);

        return $this;
    }

    /**
     * @module SprykGui|Development|ModuleFinder
     *
     * @param \SprykerSdk\Integrator\Transfer\ApplicationTransfer|null $application
     *
     * @return $this
     */
    public function setApplication(?ApplicationTransfer $application = null)
    {
        $this->application = $application;
        $this->modifiedProperties[static::APPLICATION] = true;

        return $this;
    }

    /**
     * @module SprykGui|Development|ModuleFinder
     *
     * @return \SprykerSdk\Integrator\Transfer\ApplicationTransfer|null
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @module SprykGui|Development|ModuleFinder
     *
     * @return \SprykerSdk\Integrator\Transfer\ApplicationTransfer
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
        $this->assertPropertyIsSet(static::APPLICATION);

        return $this;
    }

    /**
     * @module SprykGui
     *
     * @param \SprykerSdk\Integrator\Transfer\AbstractTransfer|null $layer
     *
     * @return $this
     */
    public function setLayer(?AbstractTransfer $layer = null)
    {
        $this->layer = $layer;
        $this->modifiedProperties[static::LAYER] = true;

        return $this;
    }

    /**
     * @module SprykGui
     *
     * @return \SprykerSdk\Integrator\Transfer\AbstractTransfer|null
     */
    public function getLayer()
    {
        return $this->layer;
    }

    /**
     * @return \SprykerSdk\Integrator\Transfer\AbstractTransfer
     */
    public function getLayerOrFail()
    {
        if ($this->layer === null) {
            $this->throwNullValueException(static::LAYER);
        }

        return $this->layer;
    }

    /**
     * @return $this
     */
    public function requireLayer()
    {
        $this->assertPropertyIsSet(static::LAYER);

        return $this;
    }

    /**
     * @param string|null $path
     *
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;
        $this->modifiedProperties[static::PATH] = true;

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
     * @return $this
     */
    public function requirePath()
    {
        $this->assertPropertyIsSet(static::PATH);

        return $this;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\AbstractTransfer|null $options
     *
     * @return $this
     */
    public function setOptions(?AbstractTransfer $options = null)
    {
        $this->options = $options;
        $this->modifiedProperties[static::OPTIONS] = true;

        return $this;
    }

    /**
     * @return \SprykerSdk\Integrator\Transfer\AbstractTransfer|null
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return \SprykerSdk\Integrator\Transfer\AbstractTransfer
     */
    public function getOptionsOrFail()
    {
        if ($this->options === null) {
            $this->throwNullValueException(static::OPTIONS);
        }

        return $this->options;
    }

    /**
     * @return $this
     */
    public function requireOptions()
    {
        $this->assertPropertyIsSet(static::OPTIONS);

        return $this;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ModuleTransfer|null $dependentModule
     *
     * @return $this
     */
    public function setDependentModule(?ModuleTransfer $dependentModule = null)
    {
        $this->dependentModule = $dependentModule;
        $this->modifiedProperties[static::DEPENDENT_MODULE] = true;

        return $this;
    }

    /**
     * @return \SprykerSdk\Integrator\Transfer\ModuleTransfer|null
     */
    public function getDependentModule()
    {
        return $this->dependentModule;
    }

    /**
     * @return \SprykerSdk\Integrator\Transfer\ModuleTransfer
     */
    public function getDependentModuleOrFail()
    {
        if ($this->dependentModule === null) {
            $this->throwNullValueException(static::DEPENDENT_MODULE);
        }

        return $this->dependentModule;
    }

    /**
     * @return $this
     */
    public function requireDependentModule()
    {
        $this->assertPropertyIsSet(static::DEPENDENT_MODULE);

        return $this;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ApplicationTransfer[]|\ArrayObject $applications
     *
     * @return $this
     */
    public function setApplications(ArrayObject $applications)
    {
        $this->applications = $applications;
        $this->modifiedProperties[static::APPLICATIONS] = true;

        return $this;
    }

    /**
     * @return array<\SprykerSdk\Integrator\Transfer\ApplicationTransfer>|\ArrayObject
     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ApplicationTransfer $application
     *
     * @return $this
     */
    public function addApplication(ApplicationTransfer $application)
    {
        $this->applications[] = $application;
        $this->modifiedProperties[static::APPLICATIONS] = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function requireApplications()
    {
        $this->assertCollectionPropertyIsSet(static::APPLICATIONS);

        return $this;
    }

    /**
     * @param bool|null $isStandalone
     *
     * @return $this
     */
    public function setIsStandalone($isStandalone)
    {
        $this->isStandalone = $isStandalone;
        $this->modifiedProperties[static::IS_STANDALONE] = true;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsStandalone()
    {
        return $this->isStandalone;
    }

    /**
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
     * @return $this
     */
    public function requireIsStandalone()
    {
        $this->assertPropertyIsSet(static::IS_STANDALONE);

        return $this;
    }

    /**
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
    public function modifiedToArray($isRecursive = true, $camelCasedKeys = false): array
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

        return [];
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

        return [];
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
    public function modifiedToArrayRecursiveCamelCased(): array
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
                case 'organization':
                case 'application':
                case 'layer':
                case 'options':
                case 'dependentModule':
                    $values[$arrayKey] = $value;

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
    public function modifiedToArrayRecursiveNotCamelCased(): array
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
                case 'organization':
                case 'application':
                case 'layer':
                case 'options':
                case 'dependentModule':
                    $values[$arrayKey] = $value;

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
    protected function initCollectionProperties(): void
    {
        $this->applications = $this->applications ?: new ArrayObject();
    }

    /**
     * @return array
     */
    public function toArrayNotRecursiveCamelCased(): array
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
    public function toArrayNotRecursiveNotCamelCased(): array
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
    public function toArrayRecursiveNotCamelCased(): array
    {
        return [
            'name' => $this->name ,
            'name_dashed' => $this->nameDashed,
            'path' => $this->path,
            'is_standalone' => $this->isStandalone,
            'organization' => $this->organization,
            'application' => $this->application,
            'layer' => $this->layer,
            'options' => $this->options,
            'dependent_module' => $this->dependentModule,
            'applications' => $this->addValuesToCollection($this->applications, true, false),
        ];
    }

    /**
     * @return array
     */
    public function toArrayRecursiveCamelCased(): array
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
            'applications' => $this->addValuesToCollection($this->applications, true, true),
        ];
    }
}
