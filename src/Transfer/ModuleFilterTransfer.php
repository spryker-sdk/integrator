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
     * @return \SprykerSdk\Integrator\Transfer\OrganizationTransfer|null
     */
    public function getOrganization(): ?OrganizationTransfer
    {
        return $this->organization;
    }

    /**
     * @return \SprykerSdk\Integrator\Transfer\OrganizationTransfer
     */
    public function getOrganizationOrFail(): OrganizationTransfer
    {
        if ($this->organization === null) {
            $this->throwNullValueException(static::ORGANIZATION);
        }

        return $this->organization;
    }

    /**
     * @return $this
     */
    public function requireOrganization()
    {
        $this->assertPropertyIsSet(static::ORGANIZATION);

        return $this;
    }

    /**
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
     * @return \SprykerSdk\Integrator\Transfer\ApplicationTransfer|null
     */
    public function getApplication(): ?ApplicationTransfer
    {
        return $this->application;
    }

    /**
     * @return \SprykerSdk\Integrator\Transfer\ApplicationTransfer
     */
    public function getApplicationOrFail(): ApplicationTransfer
    {
        if ($this->application === null) {
            $this->throwNullValueException(static::APPLICATION);
        }

        return $this->application;
    }

    /**
     * @return $this
     */
    public function requireApplication()
    {
        $this->assertPropertyIsSet(static::APPLICATION);

        return $this;
    }

    /**
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
     * @return \SprykerSdk\Integrator\Transfer\ModuleTransfer|null
     */
    public function getModule(): ?ModuleTransfer
    {
        return $this->module;
    }

    /**
     * @return \SprykerSdk\Integrator\Transfer\ModuleTransfer
     */
    public function getModuleOrFail(): ModuleTransfer
    {
        if ($this->module === null) {
            $this->throwNullValueException(static::MODULE);
        }

        return $this->module;
    }

    /**
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
    public function fromArray(array $data, bool $ignoreMissingProperty = false)
    {
        foreach ($data as $property => $value) {
            $normalizedPropertyName = $this->transferPropertyNameMap[$property] ?? '';

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
            'organization' => $this->organization,
            'application' => $this->application,
            'module' => $this->module,
        ];
    }

    /**
     * @return array
     */
    public function toArrayNotRecursiveNotCamelCased(): array
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
    public function toArrayRecursiveNotCamelCased(): array
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
    public function toArrayRecursiveCamelCased(): array
    {
        return [
            'organization' => $this->organization instanceof AbstractTransfer ? $this->organization->toArray(true, true) : $this->organization,
            'application' => $this->application instanceof AbstractTransfer ? $this->application->toArray(true, true) : $this->application,
            'module' => $this->module instanceof AbstractTransfer ? $this->module->toArray(true, true) : $this->module,
        ];
    }
}
