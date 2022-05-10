<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator\Transfer;

use InvalidArgumentException;

class ClassArgumentMetadataTransfer extends AbstractTransfer
{
    /**
     * @var string
     */
    public const VALUE = 'value';

    /**
     * @var string
     */
    public const IS_LITERAL = 'isLiteral';

    /**
     * @var string|null
     */
    protected $value;

    /**
     * @var bool|null
     */
    protected $isLiteral;

    /**
     * @var array<string, string>
     */
    protected $transferPropertyNameMap = [
        'value' => 'value',
        'Value' => 'value',
        'is_literal' => 'isLiteral',
        'isLiteral' => 'isLiteral',
        'IsLiteral' => 'isLiteral',
    ];

    /**
     * @var array<string, array<string, mixed>>
     */
    protected $transferMetadata = [
        self::VALUE => [
            'type' => 'string',
            'type_shim' => null,
            'name_underscore' => 'value',
            'is_collection' => false,
            'is_transfer' => false,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => false,
            'is_strict' => false,
        ],
        self::IS_LITERAL => [
            'type' => 'boolean',
            'type_shim' => null,
            'name_underscore' => 'is_literal',
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
     * @module App
     *
     * @param string|null $value
     *
     * @return $this
     */
    public function setValue(?string $value)
    {
        $this->value = $value;
        $this->modifiedProperties[static::VALUE] = true;

        return $this;
    }

    /**
     * @module App
     *
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @module App
     *
     * @return string
     */
    public function getValueOrFail(): string
    {
        if ($this->value === null) {
            $this->throwNullValueException(static::VALUE);
        }

        return $this->value;
    }

    /**
     * @module App
     *
     * @return $this
     */
    public function requireValue()
    {
        $this->assertPropertyIsSet(static::VALUE);

        return $this;
    }

    /**
     * @module App
     *
     * @param bool|null $isLiteral
     *
     * @return $this
     */
    public function setIsLiteral(?bool $isLiteral)
    {
        $this->isLiteral = $isLiteral;
        $this->modifiedProperties[static::IS_LITERAL] = true;

        return $this;
    }

    /**
     * @module App
     *
     * @return bool|null
     */
    public function getIsLiteral(): ?bool
    {
        return $this->isLiteral;
    }

    /**
     * @module App
     *
     * @return bool
     */
    public function getIsLiteralOrFail(): bool
    {
        if ($this->isLiteral === null) {
            $this->throwNullValueException(static::IS_LITERAL);
        }

        return $this->isLiteral;
    }

    /**
     * @module App
     *
     * @return $this
     */
    public function requireIsLiteral()
    {
        $this->assertPropertyIsSet(static::IS_LITERAL);

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
                case 'value':
                case 'isLiteral':
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
                case 'value':
                case 'isLiteral':
                    $values[$arrayKey] = $value;

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
                case 'value':
                case 'isLiteral':
                    $values[$arrayKey] = $value;

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
    }

    /**
     * @return array<string, mixed>
     */
    public function toArrayNotRecursiveCamelCased(): array
    {
        return [
            'value' => $this->value,
            'isLiteral' => $this->isLiteral,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArrayNotRecursiveNotCamelCased(): array
    {
        return [
            'value' => $this->value,
            'is_literal' => $this->isLiteral,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArrayRecursiveNotCamelCased(): array
    {
        return [
            'value' => $this->value instanceof AbstractTransfer ? $this->value->toArray(true, false) : $this->value,
            'is_literal' => $this->isLiteral instanceof AbstractTransfer ? $this->isLiteral->toArray(true, false) : $this->isLiteral,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArrayRecursiveCamelCased(): array
    {
        return [
            'value' => $this->value instanceof AbstractTransfer ? $this->value->toArray(true, true) : $this->value,
            'isLiteral' => $this->isLiteral instanceof AbstractTransfer ? $this->isLiteral->toArray(true, true) : $this->isLiteral,
        ];
    }
}
