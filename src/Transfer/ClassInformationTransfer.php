<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Transfer;

use ArrayObject;
use InvalidArgumentException;

class ClassInformationTransfer extends AbstractTransfer
{
    /**
     * @var string
     */
    public const FULLY_QUALIFIED_CLASS_NAME = 'fullyQualifiedClassName';

    /**
     * @var string
     */
    public const CLASS_NAME = 'className';

    /**
     * @var string
     */
    public const FILE_PATH = 'filePath';

    /**
     * @var string
     */
    public const PARENT = 'parent';

    /**
     * @var string
     */
    public const CLASS_TOKEN_TREE = 'classTokenTree';

    /**
     * @var string
     */
    public const ORIGINAL_CLASS_TOKEN_TREE = 'originalClassTokenTree';

    /**
     * @var string
     */
    public const TOKENS = 'tokens';

    /**
     * @var string
     */
    public const METHODS = 'methods';

    /**
     * @var string|null
     */
    protected $fullyQualifiedClassName;

    /**
     * @var string|null
     */
    protected $className;

    /**
     * @var string|null
     */
    protected $filePath;

    /**
     * @var \SprykerSdk\Integrator\Transfer\ClassInformationTransfer|null
     */
    protected $parent;

    /**
     * @var array
     */
    protected $classTokenTree = [];

    /**
     * @var array
     */
    protected $originalClassTokenTree = [];

    /**
     * @var array
     */
    protected $tokens = [];

    /**
     * @var \ArrayObject
     */
    protected $methods;

    /**
     * @var array<string, string>
     */
    protected $transferPropertyNameMap = [
        'fully_qualified_class_name' => 'fullyQualifiedClassName',
        'fullyQualifiedClassName' => 'fullyQualifiedClassName',
        'FullyQualifiedClassName' => 'fullyQualifiedClassName',
        'class_name' => 'className',
        'className' => 'className',
        'ClassName' => 'className',
        'file_path' => 'filePath',
        'filePath' => 'filePath',
        'FilePath' => 'filePath',
        'parent' => 'parent',
        'Parent' => 'parent',
        'class_token_tree' => 'classTokenTree',
        'classTokenTree' => 'classTokenTree',
        'ClassTokenTree' => 'classTokenTree',
        'original_class_token_tree' => 'originalClassTokenTree',
        'originalClassTokenTree' => 'originalClassTokenTree',
        'OriginalClassTokenTree' => 'originalClassTokenTree',
        'tokens' => 'tokens',
        'Tokens' => 'tokens',
        'methods' => 'methods',
        'Methods' => 'methods',
    ];

    /**
     * @var array
     */
    protected $transferMetadata = [
        self::FULLY_QUALIFIED_CLASS_NAME => [
            'type' => 'string',
            'type_shim' => null,
            'name_underscore' => 'fully_qualified_class_name',
            'is_collection' => false,
            'is_transfer' => false,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => false,
            'is_strict' => false,
        ],
        self::CLASS_NAME => [
            'type' => 'string',
            'type_shim' => null,
            'name_underscore' => 'class_name',
            'is_collection' => false,
            'is_transfer' => false,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => false,
            'is_strict' => false,
        ],
        self::FILE_PATH => [
            'type' => 'string',
            'type_shim' => null,
            'name_underscore' => 'file_path',
            'is_collection' => false,
            'is_transfer' => false,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => false,
            'is_strict' => false,
        ],
        self::PARENT => [
            'type' => 'Shared\Transfer\ClassInformationTransfer',
            'type_shim' => null,
            'name_underscore' => 'parent',
            'is_collection' => false,
            'is_transfer' => true,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => false,
            'is_strict' => false,
        ],
        self::CLASS_TOKEN_TREE => [
            'type' => 'array',
            'type_shim' => null,
            'name_underscore' => 'class_token_tree',
            'is_collection' => false,
            'is_transfer' => false,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => false,
            'is_strict' => false,
        ],
        self::ORIGINAL_CLASS_TOKEN_TREE => [
            'type' => 'array',
            'type_shim' => null,
            'name_underscore' => 'original_class_token_tree',
            'is_collection' => false,
            'is_transfer' => false,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => false,
            'is_strict' => false,
        ],
        self::TOKENS => [
            'type' => 'array',
            'type_shim' => null,
            'name_underscore' => 'tokens',
            'is_collection' => false,
            'is_transfer' => false,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => false,
            'is_strict' => false,
        ],
        self::METHODS => [
            'type' => 'Shared\Transfer\MethodInformationTransfer',
            'type_shim' => null,
            'name_underscore' => 'methods',
            'is_collection' => true,
            'is_transfer' => true,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => false,
            'is_strict' => false,
        ],
    ];

    /**
     * @module Integrator|SprykGui|Product
     *
     * @param string|null $fullyQualifiedClassName
     *
     * @return $this
     */
    public function setFullyQualifiedClassName($fullyQualifiedClassName)
    {
        $this->fullyQualifiedClassName = $fullyQualifiedClassName;
        $this->modifiedProperties[static::FULLY_QUALIFIED_CLASS_NAME] = true;

        return $this;
    }

    /**
     * @module Integrator|SprykGui|Product
     *
     * @return string|null
     */
    public function getFullyQualifiedClassName()
    {
        return $this->fullyQualifiedClassName;
    }

    /**
     * @module Integrator|SprykGui|Product
     *
     * @return string
     */
    public function getFullyQualifiedClassNameOrFail()
    {
        if ($this->fullyQualifiedClassName === null) {
            $this->throwNullValueException(static::FULLY_QUALIFIED_CLASS_NAME);
        }

        return $this->fullyQualifiedClassName;
    }

    /**
     * @module Integrator|SprykGui|Product
     *
     * @return $this
     */
    public function requireFullyQualifiedClassName()
    {
        $this->assertPropertyIsSet(static::FULLY_QUALIFIED_CLASS_NAME);

        return $this;
    }

    /**
     * @module Integrator|SprykGui|Product
     *
     * @param string|null $className
     *
     * @return $this
     */
    public function setClassName($className)
    {
        $this->className = $className;
        $this->modifiedProperties[static::CLASS_NAME] = true;

        return $this;
    }

    /**
     * @module Integrator|SprykGui|Product
     *
     * @return string|null
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @module Integrator|SprykGui|Product
     *
     * @return string
     */
    public function getClassNameOrFail()
    {
        if ($this->className === null) {
            $this->throwNullValueException(static::CLASS_NAME);
        }

        return $this->className;
    }

    /**
     * @module Integrator|SprykGui|Product
     *
     * @return $this
     */
    public function requireClassName()
    {
        $this->assertPropertyIsSet(static::CLASS_NAME);

        return $this;
    }

    /**
     * @module Integrator|Product
     *
     * @param string|null $filePath
     *
     * @return $this
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
        $this->modifiedProperties[static::FILE_PATH] = true;

        return $this;
    }

    /**
     * @module Integrator|Product
     *
     * @return string|null
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @module Integrator|Product
     *
     * @return string
     */
    public function getFilePathOrFail()
    {
        if ($this->filePath === null) {
            $this->throwNullValueException(static::FILE_PATH);
        }

        return $this->filePath;
    }

    /**
     * @module Integrator|Product
     *
     * @return $this
     */
    public function requireFilePath()
    {
        $this->assertPropertyIsSet(static::FILE_PATH);

        return $this;
    }

    /**
     * @module Integrator|Product
     *
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer|null $parent
     *
     * @return $this
     */
    public function setParent(?ClassInformationTransfer $parent = null)
    {
        $this->parent = $parent;
        $this->modifiedProperties[static::PARENT] = true;

        return $this;
    }

    /**
     * @module Integrator|Product
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @module Integrator|Product
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function getParentOrFail()
    {
        if ($this->parent === null) {
            $this->throwNullValueException(static::PARENT);
        }

        return $this->parent;
    }

    /**
     * @module Integrator|Product
     *
     * @return $this
     */
    public function requireParent()
    {
        $this->assertPropertyIsSet(static::PARENT);

        return $this;
    }

    /**
     * @module Integrator|Product
     *
     * @param array|null $classTokenTree
     *
     * @return $this
     */
    public function setClassTokenTree(?array $classTokenTree = null)
    {
        if ($classTokenTree === null) {
            $classTokenTree = [];
        }

        $this->classTokenTree = $classTokenTree;
        $this->modifiedProperties[static::CLASS_TOKEN_TREE] = true;

        return $this;
    }

    /**
     * @module Integrator|Product
     *
     * @return array
     */
    public function getClassTokenTree()
    {
        return $this->classTokenTree;
    }

    /**
     * @module Integrator|Product
     *
     * @param mixed $astData
     *
     * @return $this
     */
    public function addAstData($astData)
    {
        $this->classTokenTree[] = $astData;
        $this->modifiedProperties[static::CLASS_TOKEN_TREE] = true;

        return $this;
    }

    /**
     * @module Integrator|Product
     *
     * @return $this
     */
    public function requireClassTokenTree()
    {
        $this->assertPropertyIsSet(static::CLASS_TOKEN_TREE);

        return $this;
    }

    /**
     * @module Integrator|Product
     *
     * @param array|null $originalClassTokenTree
     *
     * @return $this
     */
    public function setOriginalClassTokenTree(?array $originalClassTokenTree = null)
    {
        if ($originalClassTokenTree === null) {
            $originalClassTokenTree = [];
        }

        $this->originalClassTokenTree = $originalClassTokenTree;
        $this->modifiedProperties[static::ORIGINAL_CLASS_TOKEN_TREE] = true;

        return $this;
    }

    /**
     * @module Integrator|Product
     *
     * @return array
     */
    public function getOriginalClassTokenTree()
    {
        return $this->originalClassTokenTree;
    }

    /**
     * @module Integrator|Product
     *
     * @param mixed $originalAstData
     *
     * @return $this
     */
    public function addOriginalAstData($originalAstData)
    {
        $this->originalClassTokenTree[] = $originalAstData;
        $this->modifiedProperties[static::ORIGINAL_CLASS_TOKEN_TREE] = true;

        return $this;
    }

    /**
     * @module Integrator|Product
     *
     * @return $this
     */
    public function requireOriginalClassTokenTree()
    {
        $this->assertPropertyIsSet(static::ORIGINAL_CLASS_TOKEN_TREE);

        return $this;
    }

    /**
     * @module Integrator|Product
     *
     * @param array|null $tokens
     *
     * @return $this
     */
    public function setTokens(?array $tokens = null)
    {
        if ($tokens === null) {
            $tokens = [];
        }

        $this->tokens = $tokens;
        $this->modifiedProperties[static::TOKENS] = true;

        return $this;
    }

    /**
     * @module Integrator|Product
     *
     * @return array
     */
    public function getTokens()
    {
        return $this->tokens;
    }

    /**
     * @module Integrator|Product
     *
     * @param mixed $token
     *
     * @return $this
     */
    public function addToken($token)
    {
        $this->tokens[] = $token;
        $this->modifiedProperties[static::TOKENS] = true;

        return $this;
    }

    /**
     * @module Integrator|Product
     *
     * @return $this
     */
    public function requireTokens()
    {
        $this->assertPropertyIsSet(static::TOKENS);

        return $this;
    }

    /**
     * @param \ArrayObject $methods
     *
     * @return $this
     */
    public function setMethods(ArrayObject $methods)
    {
        $this->methods = $methods;
        $this->modifiedProperties[static::METHODS] = true;

        return $this;
    }

    /**
     * @return \ArrayObject
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @module SprykGui
     *
     * @param \SprykerSdk\Integrator\Transfer\MethodInformationTransfer $method
     *
     * @return $this
     */
    public function addMethod(MethodInformationTransfer $method)
    {
        $this->methods[] = $method;
        $this->modifiedProperties[static::METHODS] = true;

        return $this;
    }

    /**
     * @module SprykGui
     *
     * @return $this
     */
    public function requireMethods()
    {
        $this->assertCollectionPropertyIsSet(static::METHODS);

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
                case 'fullyQualifiedClassName':
                case 'className':
                case 'filePath':
                case 'classTokenTree':
                case 'originalClassTokenTree':
                case 'tokens':
                    $this->$normalizedPropertyName = $value;
                    $this->modifiedProperties[$normalizedPropertyName] = true;

                    break;
                case 'parent':
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
                case 'methods':
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
                case 'fullyQualifiedClassName':
                case 'className':
                case 'filePath':
                case 'classTokenTree':
                case 'originalClassTokenTree':
                case 'parent':
                    $values[$arrayKey] = $value;

                    break;
                case 'methods':
                    $values[$arrayKey] = $value ? $this->addValuesToCollectionModified($value, true, true) : $value;

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
                case 'fullyQualifiedClassName':
                case 'className':
                case 'filePath':
                case 'classTokenTree':
                case 'originalClassTokenTree':
                case 'tokens':
                case 'parent':
                    $values[$arrayKey] = $value;

                    break;
                case 'methods':
                    $values[$arrayKey] = $value ? $this->addValuesToCollectionModified($value, true, false) : $value;

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
        $this->methods = new ArrayObject();
    }

    /**
     * @return array
     */
    public function toArrayNotRecursiveCamelCased(): array
    {
        return [
            'fullyQualifiedClassName' => $this->fullyQualifiedClassName,
            'className' => $this->className,
            'filePath' => $this->filePath,
            'classTokenTree' => $this->classTokenTree,
            'originalClassTokenTree' => $this->originalClassTokenTree,
            'tokens' => $this->tokens,
            'parent' => $this->parent,
            'methods' => $this->methods,
        ];
    }

    /**
     * @return array
     */
    public function toArrayNotRecursiveNotCamelCased(): array
    {
        return [
            'fully_qualified_class_name' => $this->fullyQualifiedClassName,
            'class_name' => $this->className,
            'file_path' => $this->filePath,
            'class_token_tree' => $this->classTokenTree,
            'original_class_token_tree' => $this->originalClassTokenTree,
            'tokens' => $this->tokens,
            'parent' => $this->parent,
            'methods' => $this->methods,
        ];
    }

    /**
     * @return array
     */
    public function toArrayRecursiveNotCamelCased(): array
    {
        return [
            'fully_qualified_class_name' => $this->fullyQualifiedClassName,
            'class_name' => $this->className,
            'file_path' => $this->filePath,
            'class_token_tree' => $this->classTokenTree,
            'original_class_token_tree' => $this->originalClassTokenTree,
            'tokens' => $this->tokens,
            'parent' => $this->parent,
            'methods' => $this->addValuesToCollection($this->methods, true, false),
        ];
    }

    /**
     * @return array
     */
    public function toArrayRecursiveCamelCased(): array
    {
        return [
            'fullyQualifiedClassName' => $this->fullyQualifiedClassName,
            'className' => $this->className,
            'filePath' => $this->filePath,
            'classTokenTree' => $this->classTokenTree,
            'originalClassTokenTree' => $this->originalClassTokenTree,
            'tokens' => $this->tokens,
            'parent' => $this->parent,
            'methods' => $this->addValuesToCollection($this->methods, true, true),
        ];
    }
}
