<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Transfer;

use ArrayAccess;
use ArrayObject;
use Countable;
use Exception;
use Serializable;

abstract class AbstractTransfer implements Serializable, ArrayAccess
{
    /**
     * @var array
     */
    protected $modifiedProperties = [];

    /**
     * @var array
     */
    protected $transferMetadata = [];

    /**
     * @var array<string>
     */
    protected $transferPropertyNameMap = [];

    public function __construct()
    {
        $this->initCollectionProperties();
    }

    /**
     * @param bool $isRecursive
     * @param bool $camelCasedKeys Set to true for camelCased keys, defaults to under_scored keys.
     *
     * @return array
     */
    public function toArray(bool $isRecursive = true, bool $camelCasedKeys = false): array
    {
        return $this->propertiesToArray($this->getPropertyNames(), $isRecursive, 'toArray', $camelCasedKeys);
    }

    /**
     * @param bool $isRecursive
     * @param bool $camelCasedKeys
     *
     * @return array
     */
    public function modifiedToArray(bool $isRecursive = true, bool $camelCasedKeys = false): array
    {
        return $this->propertiesToArray(array_keys($this->modifiedProperties), $isRecursive, 'modifiedToArray', $camelCasedKeys);
    }

    /**
     * @param string $propertyName
     *
     * @return bool
     */
    public function isPropertyModified(string $propertyName): bool
    {
        return isset($this->modifiedProperties[$propertyName]);
    }

    /**
     * @param string $propertyName
     *
     * @return bool
     */
    public function isPropertyStrict(string $propertyName): bool
    {
        if (!isset($this->transferMetadata[$propertyName]['is_strict'])) {
            return false;
        }

        return $this->transferMetadata[$propertyName]['is_strict'];
    }

    /**
     * @return void
     */
    protected function initCollectionProperties(): void
    {
        foreach ($this->transferMetadata as $property => $metaData) {
            if ($metaData['is_collection'] && $this->$property === null) {
                $this->$property = new ArrayObject();
            }
        }
    }

    /**
     * @param array $properties
     * @param bool $isRecursive
     * @param string $childConvertMethodName
     * @param bool $camelCasedKeys
     *
     * @return array
     */
    private function propertiesToArray(array $properties, bool $isRecursive, string $childConvertMethodName, bool $camelCasedKeys = false): array
    {
        $values = [];

        foreach ($properties as $property) {
            $value = $this->$property;

            $arrayKey = $this->getArrayKey($property, $camelCasedKeys);

            if (is_object($value) && $isRecursive) {
                if ($value instanceof AbstractTransfer) {
                    $values[$arrayKey] = $value->$childConvertMethodName($isRecursive, $camelCasedKeys);

                    continue;
                }

                if ($this->transferMetadata[$property]['is_collection'] && ($value instanceof Countable) && count($value) >= 1) {
                    $values = $this->addValuesToCollection($value, $values, $arrayKey, $isRecursive, $childConvertMethodName, $camelCasedKeys);

                    continue;
                }
            }

            $values[$arrayKey] = $value;
        }

        return $values;
    }

    /**
     * @param string $propertyName
     * @param bool $camelCasedKeys
     *
     * @return string
     */
    protected function getArrayKey(string $propertyName, bool $camelCasedKeys): string
    {
        if ($camelCasedKeys) {
            return $propertyName;
        }

        return $this->transferMetadata[$propertyName]['name_underscore'];
    }

    /**
     * @return array
     */
    protected function getPropertyNames(): array
    {
        return array_keys($this->transferMetadata);
    }

    /**
     * @param array<string, mixed> $data
     * @param bool $ignoreMissingProperty
     *
     * @return $this
     */
    public function fromArray(array $data, bool $ignoreMissingProperty = false)
    {
        foreach ($data as $property => $value) {
            if ($this->hasProperty($property, $ignoreMissingProperty) === false) {
                continue;
            }

            $property = $this->transferPropertyNameMap[$property];

            if ($this->transferMetadata[$property]['is_collection']) {
                $elementType = $this->transferMetadata[$property]['type'];
                $value = $this->processArrayObject($elementType, $value, $ignoreMissingProperty);
            } elseif ($this->transferMetadata[$property]['is_transfer']) {
                $value = $this->initializeNestedTransferObject($property, $value, $ignoreMissingProperty);

                if ($this->isPropertyStrict($property)) {
                    $this->assertInstanceOfTransfer($property, $value);
                }
            }

            $this->$property = $value;
            $this->modifiedProperties[$property] = true;
        }

        return $this;
    }

    /**
     * @param string $property
     * @param mixed $value
     *
     * @throws \Exception
     *
     * @return void
     */
    protected function assertInstanceOfTransfer(string $property, $value): void
    {
        if (!($value instanceof AbstractTransfer)) {
            throw new Exception(sprintf(
                'The value for the property "%s::$%s" must be an instance of AbstractTransfer.',
                static::class,
                $property,
            ));
        }
    }

    /**
     * @param string $propertyName
     * @param mixed|null $value
     *
     * @return void
     */
    protected function assignValueObject(string $propertyName, $value): void
    {
        $propertySetterMethod = $this->getSetterMethod($propertyName);
        $this->$propertySetterMethod($value);
    }

    /**
     * @param string $elementType
     * @param \ArrayObject|array $arrayObject
     * @param bool $ignoreMissingProperty
     *
     * @return \ArrayObject
     */
    protected function processArrayObject(string $elementType, $arrayObject, bool $ignoreMissingProperty = false): ArrayObject
    {
        $result = new ArrayObject();
        foreach ($arrayObject as $key => $arrayElement) {
            if (!is_array($arrayElement)) {
                $result->offsetSet($key, new $elementType());

                continue;
            }

            if ($arrayElement) {
                $transferObject = new $elementType();
                $transferObject->fromArray($arrayElement, $ignoreMissingProperty);
                $result->offsetSet($key, $transferObject);
            }
        }

        return $result;
    }

    /**
     * @param string $property
     *
     * @throws \Exception
     *
     * @return void
     */
    protected function assertPropertyIsSet(string $property): void
    {
        if ($this->$property === null) {
            throw new Exception(sprintf(
                'Missing required property "%s" for transfer %s.',
                $property,
                static::class,
            ));
        }
    }

    /**
     * @param string $property
     *
     * @throws \Exception
     *
     * @return void
     */
    protected function assertCollectionPropertyIsSet(string $property): void
    {
        /** @var \ArrayObject $collection */
        $collection = $this->$property;
        if ($collection->count() === 0) {
            throw new Exception(sprintf(
                'Empty required collection property "%s" for transfer %s.',
                $property,
                static::class,
            ));
        }
    }

    /**
     * @param string $property
     * @param mixed $value
     * @param bool $ignoreMissingProperty
     *
     * @return self
     */
    protected function initializeNestedTransferObject(string $property, $value, bool $ignoreMissingProperty = false): self
    {
        $type = $this->transferMetadata[$property]['type'];

        $transferObject = new $type();

        if (is_array($value)) {
            $transferObject->fromArray($value, $ignoreMissingProperty);
            $value = $transferObject;
        }

        return $value;
    }

    /**
     * @param string $propertyName
     *
     * @return string
     */
    protected function getSetterMethod(string $propertyName): string
    {
        return 'set' . ucfirst($propertyName);
    }

    /**
     * @param string $property
     * @param bool $ignoreMissingProperty
     *
     * @throws \Exception
     *
     * @return bool
     */
    protected function hasProperty(string $property, bool $ignoreMissingProperty): bool
    {
        if (isset($this->transferPropertyNameMap[$property])) {
            return true;
        }

        if ($ignoreMissingProperty) {
            return false;
        }

        throw new Exception(
            sprintf('Missing property "%s" in "%s"', $property, static::class),
        );
    }

    /**
     * @param mixed $value
     * @param array $values
     * @param string $arrayKey
     * @param bool $isRecursive
     * @param string $childConvertMethodName
     * @param bool $camelCasedKeys
     *
     * @return array
     */
    private function addValuesToCollection(
        $value,
        array $values,
        string $arrayKey,
        bool $isRecursive,
        string $childConvertMethodName,
        bool $camelCasedKeys = false
    ): array {
        foreach ($value as $elementKey => $arrayElement) {
            if (is_array($arrayElement) || is_scalar($arrayElement)) {
                $values[$arrayKey][$elementKey] = $arrayElement;

                continue;
            }
            $values[$arrayKey][$elementKey] = $arrayElement->$childConvertMethodName($isRecursive, $camelCasedKeys);
        }

        return $values;
    }

    /**
     * @return string
     */
    public function serialize(): string
    {
        $json = json_encode($this->modifiedToArray());

        return $json ?: '{}';
    }

    /**
     * @param mixed $serialized
     *
     * @throws \Exception
     *
     * @return void
     */
    public function unserialize($serialized): void
    {
        try {
            $this->fromArray(json_decode($serialized, true), true);
            $this->initCollectionProperties();
        } catch (Exception $exception) {
            throw new Exception(
                sprintf(
                    'Failed to unserialize %s. Updating or clearing your data source may solve this problem: %s',
                    static::class,
                    $exception->getMessage(),
                ),
                $exception->getCode(),
                $exception,
            );
        }
    }

    /**
     * @return array
     */
    public function __serialize(): array
    {
        return $this->modifiedToArray() ?: [];
    }

    /**
     * @param array $serialized
     *
     * @throws \Exception
     *
     * @return void
     */
    public function __unserialize(array $serialized): void
    {
        try {
            $this->fromArray($serialized, true);
            $this->initCollectionProperties();
        } catch (Exception $exception) {
            throw new Exception(
                sprintf(
                    'Failed to unserialize %s. Updating or clearing your data source may solve this problem: %s',
                    static::class,
                    $exception->getMessage(),
                ),
                $exception->getCode(),
                $exception,
            );
        }
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->transferMetadata[$offset]);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->$offset;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     *
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $this->$offset = $value;
    }

    /**
     * @param mixed $offset
     *
     * @throws \Exception
     *
     * @return void
     */
    public function offsetUnset($offset): void
    {
        throw new Exception('Transfer object as an array is available only for read');
    }

    /**
     * @param string $propertyName
     *
     * @throws \Exception
     *
     * @return void
     */
    protected function throwNullValueException(string $propertyName): void
    {
        throw new Exception(
            sprintf('Property "%s" of transfer `%s` is null.', $propertyName, static::class),
        );
    }
}
