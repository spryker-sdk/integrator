<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Transfer;

use InvalidArgumentException;

class OrganizationTransfer extends AbstractTransfer
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
    public const IS_PROJECT = 'isProject';

    /**
     * @var string
     */
    public const ROOT_PATH = 'rootPath';

    /**
     * @var string|null
     */
    protected $name;

    /**
     * @var string|null
     */
    protected $nameDashed;

    /**
     * @var bool|null
     */
    protected $isProject;

    /**
     * @var string|null
     */
    protected $rootPath;

    /**
     * @var array
     */
    protected $transferPropertyNameMap = [
        'name' => 'name',
        'Name' => 'name',
        'name_dashed' => 'nameDashed',
        'nameDashed' => 'nameDashed',
        'NameDashed' => 'nameDashed',
        'is_project' => 'isProject',
        'isProject' => 'isProject',
        'IsProject' => 'isProject',
        'root_path' => 'rootPath',
        'rootPath' => 'rootPath',
        'RootPath' => 'rootPath',
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
        self::IS_PROJECT => [
            'type' => 'bool',
            'type_shim' => null,
            'name_underscore' => 'is_project',
            'is_collection' => false,
            'is_transfer' => false,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => false,
            'is_strict' => false,
        ],
        self::ROOT_PATH => [
            'type' => 'string',
            'type_shim' => null,
            'name_underscore' => 'root_path',
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
     * @module SprykGui|Development|ModuleFinder
     *
     * @param bool|null $isProject
     *
     * @return $this
     */
    public function setIsProject($isProject)
    {
        $this->isProject = $isProject;
        $this->modifiedProperties[static::IS_PROJECT] = true;

        return $this;
    }

    /**
     * @module SprykGui|Development|ModuleFinder
     *
     * @return bool|null
     */
    public function getIsProject()
    {
        return $this->isProject;
    }

    /**
     * @module SprykGui|Development|ModuleFinder
     *
     * @return bool
     */
    public function getIsProjectOrFail()
    {
        if ($this->isProject === null) {
            $this->throwNullValueException(static::IS_PROJECT);
        }

        return $this->isProject;
    }

    /**
     * @module SprykGui|Development|ModuleFinder
     *
     * @return $this
     */
    public function requireIsProject()
    {
        $this->assertPropertyIsSet(static::IS_PROJECT);

        return $this;
    }

    /**
     * @module SprykGui
     *
     * @param string|null $rootPath
     *
     * @return $this
     */
    public function setRootPath($rootPath)
    {
        $this->rootPath = $rootPath;
        $this->modifiedProperties[static::ROOT_PATH] = true;

        return $this;
    }

    /**
     * @module SprykGui
     *
     * @return string|null
     */
    public function getRootPath()
    {
        return $this->rootPath;
    }

    /**
     * @module SprykGui
     *
     * @return string
     */
    public function getRootPathOrFail()
    {
        if ($this->rootPath === null) {
            $this->throwNullValueException(static::ROOT_PATH);
        }

        return $this->rootPath;
    }

    /**
     * @module SprykGui
     *
     * @return $this
     */
    public function requireRootPath()
    {
        $this->assertPropertyIsSet(static::ROOT_PATH);

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
                case 'isProject':
                case 'rootPath':
                    $this->$normalizedPropertyName = $value;
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
    public function toArray($isRecursive = true, $camelCasedKeys = false): array
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
                case 'isProject':
                case 'rootPath':
                    $values[$arrayKey] = $value;

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
                case 'isProject':
                case 'rootPath':
                    $values[$arrayKey] = $value;

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
    }

    /**
     * @return array
     */
    public function toArrayNotRecursiveCamelCased()
    {
        return [
            'name' => $this->name,
            'nameDashed' => $this->nameDashed,
            'isProject' => $this->isProject,
            'rootPath' => $this->rootPath,
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
            'is_project' => $this->isProject,
            'root_path' => $this->rootPath,
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
            'is_project' => $this->isProject instanceof AbstractTransfer ? $this->isProject->toArray(true, false) : $this->isProject,
            'root_path' => $this->rootPath instanceof AbstractTransfer ? $this->rootPath->toArray(true, false) : $this->rootPath,
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
            'isProject' => $this->isProject instanceof AbstractTransfer ? $this->isProject->toArray(true, true) : $this->isProject,
            'rootPath' => $this->rootPath instanceof AbstractTransfer ? $this->rootPath->toArray(true, true) : $this->rootPath,
        ];
    }
}
