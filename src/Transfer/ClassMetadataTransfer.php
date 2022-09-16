<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

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
     * @var string
     */
    public const CONDITION = 'condition';

    /**
     * @var string
     */
    public const TARGET_METHOD_NAME = 'targetMethodName';

    /**
     * @var string|null
     */
    protected $target;

    /**
     * @var string|null
     */
    protected $source;

    /**
     * @var \ArrayObject<\SprykerSdk\Integrator\Transfer\ClassArgumentMetadataTransfer>
     */
    protected $prependArguments;

    /**
     * @var \ArrayObject<\SprykerSdk\Integrator\Transfer\ClassArgumentMetadataTransfer>
     */
    protected $appendArguments;

    /**
     * @var \ArrayObject<\SprykerSdk\Integrator\Transfer\ClassArgumentMetadataTransfer>
     */
    protected $constructorArguments;

    /**
     * @var \ArrayObject<string>
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
     * @var string|null
     */
    protected $condition;

    /**
     * @var string|null
     */
    protected $targetMethodName;

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
        'condition' => 'condition',
        'Condition' => 'condition',
        'targetMethodName' => 'targetMethodName',
        'TargetMethodName' => 'targetMethodName',
        'target_methodName' => 'targetMethodName',
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
        self::CONDITION => [
            'type' => 'string',
            'type_shim' => null,
            'name_underscore' => 'condition',
            'is_collection' => false,
            'is_transfer' => false,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => false,
            'is_strict' => false,
        ],
        self::TARGET_METHOD_NAME => [
            'type' => 'string',
            'type_shim' => null,
            'name_underscore' => 'target_method_name',
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
     * @param \ArrayObject<\SprykerSdk\Integrator\Transfer\ClassArgumentMetadataTransfer> $prependArguments
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
     * @return \ArrayObject<\SprykerSdk\Integrator\Transfer\ClassArgumentMetadataTransfer>
     */
    public function getPrependArguments(): ArrayObject
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
     * @param \ArrayObject<\SprykerSdk\Integrator\Transfer\ClassArgumentMetadataTransfer> $appendArguments
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
     * @return \ArrayObject<\SprykerSdk\Integrator\Transfer\ClassArgumentMetadataTransfer>
     */
    public function getAppendArguments(): ArrayObject
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
     * @param \ArrayObject<\SprykerSdk\Integrator\Transfer\ClassArgumentMetadataTransfer> $constructorArguments
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
     * @return \ArrayObject<\SprykerSdk\Integrator\Transfer\ClassArgumentMetadataTransfer>
     */
    public function getConstructorArguments(): ArrayObject
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
     * @param ArrayObject $before
     * @return $this
     */
    public function setBefore(ArrayObject $before)
    {
        $this->before = $before;
        $this->modifiedProperties[static::BEFORE] = true;

        return $this;
    }

    /**
     * @return ArrayObject
     */
    public function getBefore(): ArrayObject
    {
        return $this->before;
    }

    /**
     * @param string $before
     * @return $this
     */
    public function addBefore(string $before)
    {
        $this->before[] = $before;
        $this->modifiedProperties[static::BEFORE] = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function requireBefore()
    {
        $this->assertCollectionPropertyIsSet(static::BEFORE);

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
     * @param string|null $condition
     *
     * @return $this
     */
    public function setCondition(?string $condition)
    {
        $this->condition = $condition;
        $this->modifiedProperties[static::CONDITION] = true;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCondition(): ?string
    {
        return $this->condition;
    }

    /**
     * @return string
     */
    public function getConditionOrFail(): string
    {
        if ($this->condition === null) {
            $this->throwNullValueException(static::CONDITION);
        }

        return $this->condition;
    }

    /**
     * @return $this
     */
    public function requireCondition()
    {
        $this->assertPropertyIsSet(static::CONDITION);

        return $this;
    }

    /**
     * @param string|null $targetMethodName
     *
     * @return $this
     */
    public function setTargetMethodName(?string $targetMethodName)
    {
        $this->targetMethodName = $targetMethodName;
        $this->modifiedProperties[static::TARGET_METHOD_NAME] = true;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTargetMethodName(): ?string
    {
        return $this->targetMethodName;
    }

    /**
     * @return string
     */
    public function getTargetMethodNameOrFail(): string
    {
        if ($this->targetMethodName === null) {
            $this->throwNullValueException(static::TARGET_METHOD_NAME);
        }

        return $this->targetMethodName;
    }

    /**
     * @return $this
     */
    public function requireTargetMethodName()
    {
        $this->assertPropertyIsSet(static::TARGET_METHOD_NAME);

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
                case 'after':
                case 'index':
                case 'condition':
                case 'targetMethodName':
                    $this->$normalizedPropertyName = $value;
                    $this->modifiedProperties[$normalizedPropertyName] = true;

                    break;
                case 'before':
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

        return [];
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

        return [];
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

            /** @var string $arrayKey */
            $arrayKey = $property;

            if ($value instanceof AbstractTransfer) {
                $values[$arrayKey] = $value->modifiedToArray(true, true);

                continue;
            }
            switch ($property) {
                case 'target':
                case 'source':
                case 'after':
                case 'index':
                case 'condition':
                case 'targetMethodName':
                    $values[$arrayKey] = $value;

                    break;
                case 'before':
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

            /** @var string $arrayKey */
            $arrayKey = $this->transferMetadata[$property]['name_underscore'];

            if ($value instanceof AbstractTransfer) {
                $values[$arrayKey] = $value->modifiedToArray(true, false);

                continue;
            }
            switch ($property) {
                case 'target':
                case 'source':
                case 'after':
                case 'index':
                case 'condition':
                case 'targetMethodName':
                    $values[$arrayKey] = $value;

                    break;
                case 'before':
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

            /** @var string $arrayKey */
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
        /**
         * @phpstan-ignore-next-line
         */
        $this->prependArguments = $this->prependArguments ?: new ArrayObject();

        /**
         * @phpstan-ignore-next-line
         */
        $this->appendArguments = $this->appendArguments ?: new ArrayObject();

        /**
         * @phpstan-ignore-next-line
         */
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
            'condition' => $this->condition,
            'targetMethodName' => $this->targetMethodName,
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
            'condition' => $this->condition,
            'targetMethodName' => $this->targetMethodName,
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
            'target' => $this->target,
            'source' => $this->source,
            'before' =>  $this->addValuesToCollection($this->before, true, false),
            'after' => $this->after,
            'index' => $this->index,
            'condition' => $this->condition,
            'targetMethodName' => $this->targetMethodName,
            'prepend_arguments' => $this->addValuesToCollection($this->prependArguments, true, false),
            'append_arguments' => $this->addValuesToCollection($this->appendArguments, true, false),
            'constructor_arguments' => $this->addValuesToCollection($this->constructorArguments, true, false),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArrayRecursiveCamelCased(): array
    {
        return [
            'target' => $this->target,
            'source' => $this->source,
            'before' => $this->addValuesToCollection($this->before, true, true),
            'after' => $this->after,
            'index' => $this->index,
            'condition' => $this->index,
            'targetMethodName' => $this->targetMethodName,
            'prependArguments' => $this->addValuesToCollection($this->prependArguments, true, true),
            'appendArguments' => $this->addValuesToCollection($this->appendArguments, true, true),
            'constructorArguments' => $this->addValuesToCollection($this->constructorArguments, true, true),
        ];
    }
}
