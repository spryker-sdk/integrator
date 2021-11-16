<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Transfer;

use InvalidArgumentException;

class ModuleFilterTransfer extends AbstractTransfer
{
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
    public const MODULE = 'module';

    /**
     * @var \SprykerSdk\Integrator\Transfer\OrganizationTransfer|null
     */
    protected $organization;

    /**
     * @var \SprykerSdk\Integrator\Transfer\ApplicationTransfer|null
     */
    protected $application;

    /**
     * @var \SprykerSdk\Integrator\Transfer\ModuleTransfer|null
     */
    protected $module;

    /**
     * @var array<string, string>
     */
    protected $transferPropertyNameMap = [
        'organization' => 'organization',
        'Organization' => 'organization',
        'application' => 'application',
        'Application' => 'application',
        'module' => 'module',
        'Module' => 'module',
    ];

    /**
     * @var array
     */
    protected $transferMetadata = [
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
        self::MODULE => [
            'type' => 'Shared\Transfer\ModuleTransfer',
            'type_shim' => null,
            'name_underscore' => 'module',
            'is_collection' => false,
            'is_transfer' => true,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => false,
            'is_strict' => false,
        ],
    ];

    /**
     * @module SprykGui|Development|ModuleFinder
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
     * @module SprykGui|Development|ModuleFinder
     *
     * @return \SprykerSdk\Integrator\Transfer\OrganizationTransfer|null
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @module SprykGui|Development|ModuleFinder
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
     * @module SprykGui|Development|ModuleFinder
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
     * @module SprykGui|Development|ModuleFinder
     *
     * @param \SprykerSdk\Integrator\Transfer\ModuleTransfer|null $module
     *
     * @return $this
     */
    public function setModule(?ModuleTransfer $module = null)
    {
        $this->module = $module;
        $this->modifiedProperties[static::MODULE] = true;

        return $this;
    }

    /**
     * @module SprykGui|Development|ModuleFinder
     *
     * @return \SprykerSdk\Integrator\Transfer\ModuleTransfer|null
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @module SprykGui|Development|ModuleFinder
     *
     * @return \SprykerSdk\Integrator\Transfer\ModuleTransfer
     */
    public function getModuleOrFail()
    {
        if ($this->module === null) {
            $this->throwNullValueException(static::MODULE);
        }

        return $this->module;
    }

    /**
     * @module SprykGui|Development|ModuleFinder
     *
     * @return $this
     */
    public function requireModule()
    {
        $this->assertPropertyIsSet(static::MODULE);

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
                case 'organization':
                case 'application':
                case 'module':
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
                case 'organization':
                case 'application':
                case 'module':
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
                case 'organization':
                case 'application':
                case 'module':
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
            'organization' => $this->organization,
            'application' => $this->application,
            'module' => $this->module,
        ];
    }

    /**
     * @return array
     */
    public function toArrayNotRecursiveNotCamelCased()
    {
        return [
            'organization' => $this->organization,
            'application' => $this->application,
            'module' => $this->module,
        ];
    }

    /**
     * @return array
     */
    public function toArrayRecursiveNotCamelCased()
    {
        return [
            'organization' => $this->organization instanceof AbstractTransfer ? $this->organization->toArray(true, false) : $this->organization,
            'application' => $this->application instanceof AbstractTransfer ? $this->application->toArray(true, false) : $this->application,
            'module' => $this->module instanceof AbstractTransfer ? $this->module->toArray(true, false) : $this->module,
        ];
    }

    /**
     * @return array
     */
    public function toArrayRecursiveCamelCased()
    {
        return [
            'organization' => $this->organization instanceof AbstractTransfer ? $this->organization->toArray(true, true) : $this->organization,
            'application' => $this->application instanceof AbstractTransfer ? $this->application->toArray(true, true) : $this->application,
            'module' => $this->module instanceof AbstractTransfer ? $this->module->toArray(true, true) : $this->module,
        ];
    }
}
