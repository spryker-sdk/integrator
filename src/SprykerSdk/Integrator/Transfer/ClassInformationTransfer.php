<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerSdk\Integrator\Transfer;

use ArrayObject;
use InvalidArgumentException;

class ClassInformationTransfer extends AbstractTransfer
{
    public const FULLY_QUALIFIED_CLASS_NAME = 'fullyQualifiedClassName';

    public const CLASS_NAME = 'className';

    public const FILE_PATH = 'filePath';

    public const PARENT = 'parent';

    public const CLASS_TOKEN_TREE = 'classTokenTree';

    public const ORIGINAL_CLASS_TOKEN_TREE = 'originalClassTokenTree';

    public const TOKENS = 'tokens';

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
     * @var array
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
        $this->modifiedProperties[self::FULLY_QUALIFIED_CLASS_NAME] = true;

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
        $this->assertPropertyIsSet(self::FULLY_QUALIFIED_CLASS_NAME);

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
        $this->modifiedProperties[self::CLASS_NAME] = true;

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
        $this->assertPropertyIsSet(self::CLASS_NAME);

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
        $this->modifiedProperties[self::FILE_PATH] = true;

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
        $this->assertPropertyIsSet(self::FILE_PATH);

        return $this;
    }

    /**
     * @module Integrator|Product
     *
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer|null $parent
     *
     * @return $this
     */
    public function setParent(ClassInformationTransfer $parent = null)
    {
        $this->parent = $parent;
        $this->modifiedProperties[self::PARENT] = true;

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
        $this->assertPropertyIsSet(self::PARENT);

        return $this;
    }

    /**
     * @module Integrator|Product
     *
     * @param array|null $classTokenTree
     *
     * @return $this
     */
    public function setClassTokenTree(array $classTokenTree = null)
    {
        if ($classTokenTree === null) {
            $classTokenTree = [];
        }

        $this->classTokenTree = $classTokenTree;
        $this->modifiedProperties[self::CLASS_TOKEN_TREE] = true;

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
        $this->modifiedProperties[self::CLASS_TOKEN_TREE] = true;

        return $this;
    }

    /**
     * @module Integrator|Product
     *
     * @return $this
     */
    public function requireClassTokenTree()
    {
        $this->assertPropertyIsSet(self::CLASS_TOKEN_TREE);

        return $this;
    }

    /**
     * @module Integrator|Product
     *
     * @param array|null $originalClassTokenTree
     *
     * @return $this
     */
    public function setOriginalClassTokenTree(array $originalClassTokenTree = null)
    {
        if ($originalClassTokenTree === null) {
            $originalClassTokenTree = [];
        }

        $this->originalClassTokenTree = $originalClassTokenTree;
        $this->modifiedProperties[self::ORIGINAL_CLASS_TOKEN_TREE] = true;

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
        $this->modifiedProperties[self::ORIGINAL_CLASS_TOKEN_TREE] = true;

        return $this;
    }

    /**
     * @module Integrator|Product
     *
     * @return $this
     */
    public function requireOriginalClassTokenTree()
    {
        $this->assertPropertyIsSet(self::ORIGINAL_CLASS_TOKEN_TREE);

        return $this;
    }

    /**
     * @module Integrator|Product
     *
     * @param array|null $tokens
     *
     * @return $this
     */
    public function setTokens(array $tokens = null)
    {
        if ($tokens === null) {
            $tokens = [];
        }

        $this->tokens = $tokens;
        $this->modifiedProperties[self::TOKENS] = true;

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
        $this->modifiedProperties[self::TOKENS] = true;

        return $this;
    }

    /**
     * @module Integrator|Product
     *
     * @return $this
     */
    public function requireTokens()
    {
        $this->assertPropertyIsSet(self::TOKENS);

        return $this;
    }

    /**
     * @module SprykGui
     *
     * @param \ArrayObject|\SprykerSdk\Integrator\Transfer\MethodInformationTransfer[] $methods
     *
     * @return $this
     */
    public function setMethods(ArrayObject $methods)
    {
        $this->methods = $methods;
        $this->modifiedProperties[self::METHODS] = true;

        return $this;
    }

    /**
     * @module SprykGui
     *
     * @return \ArrayObject|\SprykerSdk\Integrator\Transfer\MethodInformationTransfer[]
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
        $this->modifiedProperties[self::METHODS] = true;

        return $this;
    }

    /**
     * @module SprykGui
     *
     * @return $this
     */
    public function requireMethods()
    {
        $this->assertCollectionPropertyIsSet(self::METHODS);

        return $this;
    }

    /**
     * @param array $data
     * @param bool $ignoreMissingProperty
     * @return ClassInformationTransfer
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
    * @return array
    */
    public function modifiedToArray($isRecursive = true, $camelCasedKeys = false)
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
    * @return array
    */
    public function toArray($isRecursive = true, $camelCasedKeys = false)
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
    * @param mixed $value
    * @param bool $isRecursive
    * @param bool $camelCasedKeys
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
                case 'tokens':
                    $values[$arrayKey] = $value;
                    break;
                case 'parent':
                    $values[$arrayKey] = $value instanceof AbstractTransfer ? $value->modifiedToArray(true, true) : $value;
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
                case 'fullyQualifiedClassName':
                case 'className':
                case 'filePath':
                case 'classTokenTree':
                case 'originalClassTokenTree':
                case 'tokens':
                    $values[$arrayKey] = $value;
                    break;
                case 'parent':
                    $values[$arrayKey] = $value instanceof AbstractTransfer ? $value->modifiedToArray(true, false) : $value;
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
        $this->methods = $this->methods ?: new ArrayObject();
    }

    /**
    * @return array
    */
    public function toArrayNotRecursiveCamelCased()
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
    public function toArrayNotRecursiveNotCamelCased()
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
    public function toArrayRecursiveNotCamelCased()
    {
        return [
            'fully_qualified_class_name' => $this->fullyQualifiedClassName instanceof AbstractTransfer ? $this->fullyQualifiedClassName->toArray(true, false) : $this->fullyQualifiedClassName,
            'class_name' => $this->className instanceof AbstractTransfer ? $this->className->toArray(true, false) : $this->className,
            'file_path' => $this->filePath instanceof AbstractTransfer ? $this->filePath->toArray(true, false) : $this->filePath,
            'class_token_tree' => $this->classTokenTree instanceof AbstractTransfer ? $this->classTokenTree->toArray(true, false) : $this->classTokenTree,
            'original_class_token_tree' => $this->originalClassTokenTree instanceof AbstractTransfer ? $this->originalClassTokenTree->toArray(true, false) : $this->originalClassTokenTree,
            'tokens' => $this->tokens instanceof AbstractTransfer ? $this->tokens->toArray(true, false) : $this->tokens,
            'parent' => $this->parent instanceof AbstractTransfer ? $this->parent->toArray(true, false) : $this->parent,
            'methods' => $this->methods instanceof AbstractTransfer ? $this->methods->toArray(true, false) : $this->addValuesToCollection($this->methods, true, false),
        ];
    }

    /**
    * @return array
    */
    public function toArrayRecursiveCamelCased()
    {
        return [
            'fullyQualifiedClassName' => $this->fullyQualifiedClassName instanceof AbstractTransfer ? $this->fullyQualifiedClassName->toArray(true, true) : $this->fullyQualifiedClassName,
            'className' => $this->className instanceof AbstractTransfer ? $this->className->toArray(true, true) : $this->className,
            'filePath' => $this->filePath instanceof AbstractTransfer ? $this->filePath->toArray(true, true) : $this->filePath,
            'classTokenTree' => $this->classTokenTree instanceof AbstractTransfer ? $this->classTokenTree->toArray(true, true) : $this->classTokenTree,
            'originalClassTokenTree' => $this->originalClassTokenTree instanceof AbstractTransfer ? $this->originalClassTokenTree->toArray(true, true) : $this->originalClassTokenTree,
            'tokens' => $this->tokens instanceof AbstractTransfer ? $this->tokens->toArray(true, true) : $this->tokens,
            'parent' => $this->parent instanceof AbstractTransfer ? $this->parent->toArray(true, true) : $this->parent,
            'methods' => $this->methods instanceof AbstractTransfer ? $this->methods->toArray(true, true) : $this->addValuesToCollection($this->methods, true, true),
        ];
    }
}
