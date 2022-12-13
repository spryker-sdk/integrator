<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Transfer;

use ArrayObject;
use PhpParser\Node\Stmt\Expression;

class ExpressionPartialParserResultTransfer extends AbstractTransfer
{
    /**
     * @var string
     */
    protected const USED_CLASSES = 'usedClasses';

    /**
     * @var string
     */
    protected const EXPRESSION = 'expression';

    /**
     * @var \ArrayObject<string>
     */
    protected ArrayObject $usedClasses;

    /**
     * @var \PhpParser\Node\Stmt\Expression
     */
    protected Expression $expression;

    /**
     * @var array<string, string>
     */
    protected $transferPropertyNameMap = [
        'fully_qualified_class_names' => 'fullyQualifiedClassNames',
        'expression_node' => 'expression',
    ];

    /**
     * @var array
     */
    protected $transferMetadata = [
        self::USED_CLASSES => [
            'type' => 'iterable',
            'type_shim' => null,
            'name_underscore' => 'used_classes',
            'is_collection' => true,
            'is_transfer' => false,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => false,
            'is_strict' => false,
        ],
        self::EXPRESSION => [
            'type' => 'PhpParser\Node\Expr',
            'type_shim' => null,
            'name_underscore' => 'expression',
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
     * @param \ArrayObject<string> $usedClassed
     * @param \PhpParser\Node\Stmt\Expression $expression
     */
    public function __construct(ArrayObject $usedClassed, Expression $expression)
    {
        $this->usedClasses = $usedClassed;
        $this->expression = $expression;

        parent::__construct();
    }

    /**
     * @return \ArrayObject<string>
     */
    public function getUsedClasses(): ArrayObject
    {
        return $this->usedClasses;
    }

    /**
     * @return \PhpParser\Node\Stmt\Expression
     */
    public function getExpression(): Expression
    {
        return $this->expression;
    }
}
