<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Transfer;

use InvalidArgumentException;

class CallMetadataTransfer extends AbstractTransfer
{
    /**
     * @var string
     */
    public const TARGET = 'target';

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
    protected $target;

    /**
     * @var string|null
     */
    protected $before;

    /**
     * @var string|null
     */
    protected $after;

    /**
     * @var array<string, string>
     */
    protected $transferPropertyNameMap = [
        'target' => 'target',
        'Target' => 'target',
        'before' => 'before',
        'Before' => 'before',
        'after' => 'after',
        'After' => 'after',
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
        self::BEFORE => [
            'type' => 'string',
            'type_shim' => null,
            'name_underscore' => 'before',
            'is_collection' => false,
            'is_transfer' => false,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => true,
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
            'is_nullable' => true,
            'is_strict' => false,
        ],
    ];

    /**
     * @module App
     *
     * @param string $target
     *
     * @return $this
     */
    public function setTarget(string $target)
    {
        $this->target = $target;
        $this->modifiedProperties[static::TARGET] = true;

        return $this;
    }

    /**
     * @module App
     *
     * @return string
     */
    public function getTarget(): string
    {
        return $this->target;
    }

    /**
     * @module App
     *
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
     * @module App
     *
     * @return $this
     */
    public function requireTarget()
    {
        $this->assertPropertyIsSet(static::TARGET);

        return $this;
    }

    /**
     * @module App
     *
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
     * @module App
     *
     * @return string|null
     */
    public function getBefore(): ?string
    {
        return $this->before;
    }

    /**
     * @module App
     *
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
     * @module App
     *
     * @return $this
     */
    public function requireBefore()
    {
        $this->assertPropertyIsSet(static::BEFORE);

        return $this;
    }

    /**
     * @module App
     *
     * @param string|null $after
     *
     * @return $this
     */
    public function setAfter(?string $after)
    {
        $this->before = $after;
        $this->modifiedProperties[static::AFTER] = true;

        return $this;
    }

    /**
     * @module App
     *
     * @return string|null
     */
    public function getAfter(): ?string
    {
        return $this->after;
    }

    /**
     * @module App
     *
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
     * @module App
     *
     * @return $this
     */
    public function requireAfter()
    {
        $this->assertPropertyIsSet(static::AFTER);

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
                case 'before':
                case 'after':
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
}
