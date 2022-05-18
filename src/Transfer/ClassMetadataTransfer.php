<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator\Transfer;

use ArrayObject;
use InvalidArgumentException;

class ClassMetadataTransfer extends AbstractTransfer
{
    /**
     * @var string
     */
    public const TARGET = 'target';

    /**
     * @var string
     */
    public const SOURCE = 'source';

    /**
     * @var string
     */
    public const PREPEND_ARGUMENTS = 'prependArguments';

    /**
     * @var string
     */
    public const APPEND_ARGUMENTS = 'appendArguments';

    /**
     * @var string
     */
    public const CONSTRUCTOR_ARGUMENTS = 'constructorArguments';

    /**
     * @var string
     */
    public const BEFORE = 'before';

    /**
     * @var string
     */
    public const AFTER = 'after';

    /**
     * @var string
     */
    public const INDEX = 'index';

    /**
     * @var string|null
     */
    protected $target;

    /**
     * @var string|null
     */
    protected $source;

    /**
     * @var \SprykerSdk\Integrator\Transfer\ClassArgumentMetadataTransfer[]|\ArrayObject
     */
    protected $prependArguments;

    /**
     * @var \SprykerSdk\Integrator\Transfer\ClassArgumentMetadataTransfer[]|\ArrayObject
     */
    protected $appendArguments;

    /**
     * @var \SprykerSdk\Integrator\Transfer\ClassArgumentMetadataTransfer[]|\ArrayObject
     */
    protected $constructorArguments;

    /**
     * @var string|null
     */
    protected $before;

    /**
     * @var string|null
     */
    protected $after;

    /**
     * @var string|null
     */
    protected $index;

    /**
     * @var array<string, string>
     */
    protected $transferPropertyNameMap = [
        'target' => 'target',
        'Target' => 'target',
        'source' => 'source',
        'Source' => 'source',
        'prepend_arguments' => 'prependArguments',
        'prependArguments' => 'prependArguments',
        'PrependArguments' => 'prependArguments',
        'append_arguments' => 'appendArguments',
        'appendArguments' => 'appendArguments',
        'AppendArguments' => 'appendArguments',
        'constructor_arguments' => 'constructorArguments',
        'constructorArguments' => 'constructorArguments',
        'ConstructorArguments' => 'constructorArguments',
        'before' => 'before',
        'Before' => 'before',
        'after' => 'after',
        'After' => 'after',
        'index' => 'index',
        'Index' => 'index',
    ];

    /**
     * @var array<string, array<string, mixed>>
     */
    protected $transferMetadata = [
        self::TARGET => [
            'type' => 'string',
            'type_shim' => null,
            'name_underscore' => 'target',
            'is_collection' => false,
            'is_transfer' => false,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => false,
            'is_strict' => false,
        ],
        self::SOURCE => [
            'type' => 'string',
            'type_shim' => null,
            'name_underscore' => 'source',
            'is_collection' => false,
            'is_transfer' => false,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => false,
            'is_strict' => false,
        ],
        self::PREPEND_ARGUMENTS => [
            'type' => 'SprykerSdk\Integrator\Transfer\ClassArgumentMetadataTransfer',
            'type_shim' => null,
            'name_underscore' => 'prepend_arguments',
            'is_collection' => true,
            'is_transfer' => true,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => false,
            'is_strict' => false,
        ],
        self::APPEND_ARGUMENTS => [
            'type' => 'SprykerSdk\Integrator\Transfer\ClassArgumentMetadataTransfer',
            'type_shim' => null,
            'name_underscore' => 'append_arguments',
            'is_collection' => true,
            'is_transfer' => true,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => false,
            'is_strict' => false,
        ],
        self::CONSTRUCTOR_ARGUMENTS => [
            'type' => 'SprykerSdk\Integrator\Transfer\ClassArgumentMetadataTransfer',
            'type_shim' => null,
            'name_underscore' => 'constructor_arguments',
            'is_collection' => true,
            'is_transfer' => true,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => false,
            'is_strict' => false,
        ],
        self::BEFORE => [
            'type' => 'string',
            'type_shim' => null,
            'name_underscore' => 'before',
            'is_collection' => false,
            'is_transfer' => false,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => false,
            'is_strict' => false,
        ],
        self::AFTER => [
            'type' => 'string',
            'type_shim' => null,
            'name_underscore' => 'after',
            'is_collection' => false,
            'is_transfer' => false,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => false,
            'is_strict' => false,
        ],
        self::INDEX => [
            'type' => 'string',
            'type_shim' => null,
            'name_underscore' => 'index',
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
     * @param string|null $target
     *
     * @return $this
     */
    public function setTarget(?string $target)
    {
        $this->target = $target;
        $this->modifiedProperties[static::TARGET] = true;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTarget(): ?string
    {
        return $this->target;
    }

    /**
     * @return string
     */
    public function getTargetOrFail(): string
    {
        if ($this->target === null) {
            $this->throwNullValueException(static::TARGET);
        }

        return $this->target;
    }

    /**
     * @return $this
     */
    public function requireTarget()
    {
        $this->assertPropertyIsSet(static::TARGET);

        return $this;
    }

    /**
     * @param string|null $source
     *
     * @return $this
     */
    public function setSource(?string $source)
    {
        $this->source = $source;
        $this->modifiedProperties[static::SOURCE] = true;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSource(): ?string
    {
        return $this->source;
    }

    /**
     * @return string
     */
    public function getSourceOrFail(): string
    {
        if ($this->source === null) {
            $this->throwNullValueException(static::SOURCE);
        }

        return $this->source;
    }

    /**
     * @return $this
     */
    public function requireSource()
    {
        $this->assertPropertyIsSet(static::SOURCE);

        return $this;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassArgumentMetadataTransfer[]|\ArrayObject $prependArguments
     *
     * @return $this
     */
    public function setPrependArguments(ArrayObject $prependArguments)
    {
        $this->prependArguments = $prependArguments;
        $this->modifiedProperties[static::PREPEND_ARGUMENTS] = true;

        return $this;
    }

    /**
     * @return \SprykerSdk\Integrator\Transfer\ClassArgumentMetadataTransfer[]|\ArrayObject
     */
    public function getPrependArguments()
    {
        return $this->prependArguments;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassArgumentMetadataTransfer $prependArguments
     *
     * @return $this
     */
    public function addPrependArguments(ClassArgumentMetadataTransfer $prependArguments)
    {
        $this->prependArguments[] = $prependArguments;
        $this->modifiedProperties[static::PREPEND_ARGUMENTS] = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function requirePrependArguments()
    {
        $this->assertCollectionPropertyIsSet(static::PREPEND_ARGUMENTS);

        return $this;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassArgumentMetadataTransfer[]|\ArrayObject $appendArguments
     *
     * @return $this
     */
    public function setAppendArguments(ArrayObject $appendArguments)
    {
        $this->appendArguments = $appendArguments;
        $this->modifiedProperties[static::APPEND_ARGUMENTS] = true;

        return $this;
    }

    /**
     * @module App
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassArgumentMetadataTransfer[]|\ArrayObject
     */
    public function getAppendArguments()
    {
        return $this->appendArguments;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassArgumentMetadataTransfer $appendArguments
     *
     * @return $this
     */
    public function addAppendArguments(ClassArgumentMetadataTransfer $appendArguments)
    {
        $this->appendArguments[] = $appendArguments;
        $this->modifiedProperties[static::APPEND_ARGUMENTS] = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function requireAppendArguments()
    {
        $this->assertCollectionPropertyIsSet(static::APPEND_ARGUMENTS);

        return $this;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassArgumentMetadataTransfer[]|\ArrayObject $constructorArguments
     *
     * @return $this
     */
    public function setConstructorArguments(ArrayObject $constructorArguments)
    {
        $this->constructorArguments = $constructorArguments;
        $this->modifiedProperties[static::CONSTRUCTOR_ARGUMENTS] = true;

        return $this;
    }

    /**
     * @return \SprykerSdk\Integrator\Transfer\ClassArgumentMetadataTransfer[]|\ArrayObject
     */
    public function getConstructorArguments()
    {
        return $this->constructorArguments;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassArgumentMetadataTransfer $constructorArguments
     *
     * @return $this
     */
    public function addConstructorArguments(ClassArgumentMetadataTransfer $constructorArguments)
    {
        $this->constructorArguments[] = $constructorArguments;
        $this->modifiedProperties[static::CONSTRUCTOR_ARGUMENTS] = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function requireConstructorArguments()
    {
        $this->assertCollectionPropertyIsSet(static::CONSTRUCTOR_ARGUMENTS);

        return $this;
    }

    /**
     * @param string|null $before
     *
     * @return $this
     */
    public function setBefore(?string $before)
    {
        $this->before = $before;
        $this->modifiedProperties[static::BEFORE] = true;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getBefore(): ?string
    {
        return $this->before;
    }

    /**
     * @return string
     */
    public function getBeforeOrFail(): string
    {
        if ($this->before === null) {
            $this->throwNullValueException(static::BEFORE);
        }

        return $this->before;
    }

    /**
     * @return $this
     */
    public function requireBefore()
    {
        $this->assertPropertyIsSet(static::BEFORE);

        return $this;
    }

    /**
     * @param string|null $after
     *
     * @return $this
     */
    public function setAfter(?string $after)
    {
        $this->after = $after;
        $this->modifiedProperties[static::AFTER] = true;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAfter(): ?string
    {
        return $this->after;
    }

    /**
     * @return string
     */
    public function getAfterOrFail(): string
    {
        if ($this->after === null) {
            $this->throwNullValueException(static::AFTER);
        }

        return $this->after;
    }

    /**
     * @return $this
     */
    public function requireAfter()
    {
        $this->assertPropertyIsSet(static::AFTER);

        return $this;
    }

    /**
     * @param string|null $index
     *
     * @return $this
     */
    public function setIndex(?string $index)
    {
        $this->index = $index;
        $this->modifiedProperties[static::INDEX] = true;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getIndex(): ?string
    {
        return $this->index;
    }

    /**
     * @return string
     */
    public function getIndexOrFail(): string
    {
        if ($this->index === null) {
            $this->throwNullValueException(static::INDEX);
        }

        return $this->index;
    }

    /**
     * @return $this
     */
    public function requireIndex()
    {
        $this->assertPropertyIsSet(static::INDEX);

        return $this;
    }

    /**
     * @param array<string, mixed> $data
     * @param bool $ignoreMissingProperty
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function fromArray(array $data, bool $ignoreMissingProperty = false)
    {
        foreach ($data as $property => $value) {
            $normalizedPropertyName = $this->transferPropertyNameMap[$property] ?? null;

            switch ($normalizedPropertyName) {
                case 'target':
                case 'source':
                case 'before':
                case 'after':
                case 'index':
                    $this->$normalizedPropertyName = $value;
                    $this->modifiedProperties[$normalizedPropertyName] = true;

                    break;
                case 'prependArguments':
                case 'appendArguments':
                case 'constructorArguments':
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
     * @return array<string, mixed>
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
    }

    /**
     * @param bool $isRecursive
     * @param bool $camelCasedKeys
     *
     * @return array<string, mixed>
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
    }

    /**
     * @param \ArrayObject<string, mixed>|array<string, mixed> $value
     * @param bool $isRecursive
     * @param bool $camelCasedKeys
     *
     * @return array<string, mixed>
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
     * @param \ArrayObject<string, mixed>|array<string, mixed> $value
     * @param bool $isRecursive
     * @param bool $camelCasedKeys
     *
     * @return array<string, mixed>
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
     * @return array<string, mixed>
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
                case 'target':
                case 'source':
                case 'before':
                case 'after':
                case 'index':
                    $values[$arrayKey] = $value;

                    break;
                case 'prependArguments':
                case 'appendArguments':
                case 'constructorArguments':
                    $values[$arrayKey] = $value ? $this->addValuesToCollectionModified($value, true, true) : $value;

                    break;
            }
        }

        return $values;
    }

    /**
     * @return array<string, mixed>
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
                case 'target':
                case 'source':
                case 'before':
                case 'after':
                case 'index':
                    $values[$arrayKey] = $value;

                    break;
                case 'prependArguments':
                case 'appendArguments':
                case 'constructorArguments':
                    $values[$arrayKey] = $value ? $this->addValuesToCollectionModified($value, true, false) : $value;

                    break;
            }
        }

        return $values;
    }

    /**
     * @return array<string, mixed>
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
     * @return array<string, mixed>
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
        $this->prependArguments = $this->prependArguments ?: new ArrayObject();
        $this->appendArguments = $this->appendArguments ?: new ArrayObject();
        $this->constructorArguments = $this->constructorArguments ?: new ArrayObject();
    }

    /**
     * @return array<string, mixed>
     */
    public function toArrayNotRecursiveCamelCased(): array
    {
        return [
            'target' => $this->target,
            'source' => $this->source,
            'before' => $this->before,
            'after' => $this->after,
            'index' => $this->index,
            'prependArguments' => $this->prependArguments,
            'appendArguments' => $this->appendArguments,
            'constructorArguments' => $this->constructorArguments,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArrayNotRecursiveNotCamelCased(): array
    {
        return [
            'target' => $this->target,
            'source' => $this->source,
            'before' => $this->before,
            'after' => $this->after,
            'index' => $this->index,
            'prepend_arguments' => $this->prependArguments,
            'append_arguments' => $this->appendArguments,
            'constructor_arguments' => $this->constructorArguments,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArrayRecursiveNotCamelCased(): array
    {
        return [
            'target' => $this->target instanceof AbstractTransfer ? $this->target->toArray(true, false) : $this->target,
            'source' => $this->source instanceof AbstractTransfer ? $this->source->toArray(true, false) : $this->source,
            'before' => $this->before instanceof AbstractTransfer ? $this->before->toArray(true, false) : $this->before,
            'after' => $this->after instanceof AbstractTransfer ? $this->after->toArray(true, false) : $this->after,
            'index' => $this->index instanceof AbstractTransfer ? $this->index->toArray(true, false) : $this->index,
            'prepend_arguments' => $this->prependArguments instanceof AbstractTransfer ? $this->prependArguments->toArray(true, false) : $this->addValuesToCollection($this->prependArguments, true, false),
            'append_arguments' => $this->appendArguments instanceof AbstractTransfer ? $this->appendArguments->toArray(true, false) : $this->addValuesToCollection($this->appendArguments, true, false),
            'constructor_arguments' => $this->constructorArguments instanceof AbstractTransfer ? $this->constructorArguments->toArray(true, false) : $this->addValuesToCollection($this->constructorArguments, true, false),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArrayRecursiveCamelCased(): array
    {
        return [
            'target' => $this->target instanceof AbstractTransfer ? $this->target->toArray(true, true) : $this->target,
            'source' => $this->source instanceof AbstractTransfer ? $this->source->toArray(true, true) : $this->source,
            'before' => $this->before instanceof AbstractTransfer ? $this->before->toArray(true, true) : $this->before,
            'after' => $this->after instanceof AbstractTransfer ? $this->after->toArray(true, true) : $this->after,
            'index' => $this->index instanceof AbstractTransfer ? $this->index->toArray(true, true) : $this->index,
            'prependArguments' => $this->prependArguments instanceof AbstractTransfer ? $this->prependArguments->toArray(true, true) : $this->addValuesToCollection($this->prependArguments, true, true),
            'appendArguments' => $this->appendArguments instanceof AbstractTransfer ? $this->appendArguments->toArray(true, true) : $this->addValuesToCollection($this->appendArguments, true, true),
            'constructorArguments' => $this->constructorArguments instanceof AbstractTransfer ? $this->constructorArguments->toArray(true, true) : $this->addValuesToCollection($this->constructorArguments, true, true),
        ];
    }
}
