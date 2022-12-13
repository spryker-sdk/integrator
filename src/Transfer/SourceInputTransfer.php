<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Transfer;

use InvalidArgumentException;

class SourceInputTransfer extends AbstractTransfer
{
    /**
     * @var string
     */
    public const SOURCE = 'source';

    /**
     * @var string
     */
    public const SOURCE_COMMIT = 'sourceCommit';

    /**
     * @var string|null
     */
    protected $source;

    /**
     * @var string|null
     */
    protected $sourceCommit;

    /**
     * @var array<string, string>
     */
    protected $transferPropertyNameMap = [
        'source' => 'source',
        'Source' => 'source',
        'source_commit' => 'sourceCommit',
        'sourceCommit' => 'sourceCommit',
        'SourceCommit' => 'sourceCommit',
    ];

    /**
     * @var array
     */
    protected $transferMetadata = [
        self::SOURCE => [
            'type' => 'string',
            'type_shim' => null,
            'name_underscore' => 'name',
            'is_collection' => false,
            'is_transfer' => false,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => true,
            'is_strict' => false,
        ],
        self::SOURCE_COMMIT => [
            'type' => 'string',
            'type_shim' => null,
            'name_underscore' => 'name',
            'is_collection' => false,
            'is_transfer' => false,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => true,
            'is_strict' => false,
        ],
    ];

    /**
     * @param string|null $name
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
     * @param string|null $name
     *
     * @return $this
     */
    public function setSourceCommit(?string $sourceCommit)
    {
        $this->sourceCommit = $sourceCommit;
        $this->modifiedProperties[static::SOURCE_COMMIT] = true;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSourceCommit(): ?string
    {
        return $this->sourceCommit;
    }

    /**
     * @return string
     */
    public function getSourceCommitOrFail(): string
    {
        if ($this->sourceCommit === null) {
            $this->throwNullValueException(static::SOURCE_COMMIT);
        }

        return $this->sourceCommit;
    }

    /**
     * @return $this
     */
    public function requireSourceCommit()
    {
        $this->assertPropertyIsSet(static::SOURCE_COMMIT);

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
            'source' => $this->source,
            'sourceCommit' => $this->sourceCommit,
        ];
    }

    /**
     * @return array
     */
    public function toArrayNotRecursiveNotCamelCased(): array
    {
        return [
            'source' => $this->source,
            'source_commit' => $this->sourceCommit,
        ];
    }

    /**
     * @return array
     */
    public function toArrayRecursiveNotCamelCased(): array
    {
        return [
            'source' => $this->source,
            'source_commit' => $this->sourceCommit,
        ];
    }

    /**
     * @return array
     */
    public function toArrayRecursiveCamelCased(): array
    {
        return [
            'source' => $this->source,
            'sourceCommit' => $this->sourceCommit,
        ];
    }
}
