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
     * @var array<string, string>
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
     * @param string|null $name
     *
     * @return $this
     */
    public function setName(?string $name)
    {
        $this->name = $name;
        $this->modifiedProperties[static::NAME] = true;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getNameOrFail(): string
    {
        if ($this->name === null) {
            $this->throwNullValueException(static::NAME);
        }

        return $this->name;
    }

    /**
     * @return $this
     */
    public function requireName()
    {
        $this->assertPropertyIsSet(static::NAME);

        return $this;
    }

    /**
     * @param string|null $nameDashed
     *
     * @return $this
     */
    public function setNameDashed(?string $nameDashed)
    {
        $this->nameDashed = $nameDashed;
        $this->modifiedProperties[static::NAME_DASHED] = true;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNameDashed(): ?string
    {
        return $this->nameDashed;
    }

    /**
     * @return string
     */
    public function getNameDashedOrFail(): string
    {
        if ($this->nameDashed === null) {
            $this->throwNullValueException(static::NAME_DASHED);
        }

        return $this->nameDashed;
    }

    /**
     * @return $this
     */
    public function requireNameDashed()
    {
        $this->assertPropertyIsSet(static::NAME_DASHED);

        return $this;
    }

    /**
     * @param bool|null $isProject
     *
     * @return $this
     */
    public function setIsProject(?bool $isProject)
    {
        $this->isProject = $isProject;
        $this->modifiedProperties[static::IS_PROJECT] = true;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsProject(): ?bool
    {
        return $this->isProject;
    }

    /**
     * @return bool
     */
    public function getIsProjectOrFail(): bool
    {
        if ($this->isProject === null) {
            $this->throwNullValueException(static::IS_PROJECT);
        }

        return $this->isProject;
    }

    /**
     * @return $this
     */
    public function requireIsProject()
    {
        $this->assertPropertyIsSet(static::IS_PROJECT);

        return $this;
    }

    /**
     * @param string|null $rootPath
     *
     * @return $this
     */
    public function setRootPath(?string $rootPath)
    {
        $this->rootPath = $rootPath;
        $this->modifiedProperties[static::ROOT_PATH] = true;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRootPath(): ?string
    {
        return $this->rootPath;
    }

    /**
     * @return string
     */
    public function getRootPathOrFail(): string
    {
        if ($this->rootPath === null) {
            $this->throwNullValueException(static::ROOT_PATH);
        }

        return $this->rootPath;
    }

    /**
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
    public function fromArray(array $data, bool $ignoreMissingProperty = false)
    {
        foreach ($data as $property => $value) {
            $normalizedPropertyName = $this->transferPropertyNameMap[$property] ?? '';

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
    public function modifiedToArray(bool $isRecursive = true, bool $camelCasedKeys = false): array
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
    public function toArray(bool $isRecursive = true, bool $camelCasedKeys = false): array
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
    protected function addValuesToCollectionModified($value, bool $isRecursive, bool $camelCasedKeys): array
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
    protected function addValuesToCollection($value, bool $isRecursive, bool $camelCasedKeys): array
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
    public function modifiedToArrayNotRecursiveNotCamelCased(): array
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
    public function modifiedToArrayNotRecursiveCamelCased(): array
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
    }

    /**
     * @return array
     */
    public function toArrayNotRecursiveCamelCased(): array
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
    public function toArrayNotRecursiveNotCamelCased(): array
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
    public function toArrayRecursiveNotCamelCased(): array
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
    public function toArrayRecursiveCamelCased(): array
    {
        return [
            'name' => $this->name,
            'nameDashed' => $this->nameDashed,
            'isProject' => $this->isProject,
            'rootPath' => $this->rootPath,
        ];
    }
}
