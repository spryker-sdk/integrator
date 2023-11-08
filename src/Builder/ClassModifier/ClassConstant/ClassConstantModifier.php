<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\ClassModifier\ClassConstant;

use PhpParser\Node\Expr;
use PhpParser\ParserFactory;
use SprykerSdk\Integrator\Builder\ClassModifier\AddVisitorsTrait;
use SprykerSdk\Integrator\Builder\Exception\LiteralValueParsingException;
use SprykerSdk\Integrator\Builder\Finder\ClassConstantFinderInterface;
use SprykerSdk\Integrator\Builder\Visitor\AddConstantVisitor;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;

class ClassConstantModifier implements ClassConstantModifierInterface
{
    use AddVisitorsTrait;

    /**
     * @var int
     */
    protected const SINGLE_EXPRESSION_COUNT = 1;

    /**
     * @var \PhpParser\ParserFactory
     */
    private ParserFactory $parserFactory;

    /**
     * @var \SprykerSdk\Integrator\Builder\Finder\ClassConstantFinderInterface
     */
    protected ClassConstantFinderInterface $classConstantFinder;

    /**
     * @param \PhpParser\ParserFactory $parserFactory
     * @param \SprykerSdk\Integrator\Builder\Finder\ClassConstantFinderInterface $classConstantFinder
     */
    public function __construct(ParserFactory $parserFactory, ClassConstantFinderInterface $classConstantFinder)
    {
        $this->parserFactory = $parserFactory;
        $this->classConstantFinder = $classConstantFinder;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $constantName
     * @param mixed $value
     * @param bool $isLiteral
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function setConstant(
        ClassInformationTransfer $classInformationTransfer,
        string $constantName,
        $value,
        bool $isLiteral
    ): ClassInformationTransfer {
        $parentConstant = $this->classConstantFinder->findParentConstantByName($classInformationTransfer, $constantName);

        $modifier = 'public';
        if ($parentConstant) {
            if ($parentConstant->isProtected()) {
                $modifier = 'protected';
            } elseif ($parentConstant->isPrivate()) {
                $modifier = 'private';
            }
        }

        if ($isLiteral) {
            $value = $this->parseSingleValue((string)$value);
        } elseif (is_array($value)) {
            foreach ($value as $idx => $val) {
                if (!is_string($val) || strpos($val, '::') === false) {
                    continue;
                }

                $value[$idx] = $this->parseSingleValue($val);
            }
        }

        $visitors = [
            new AddConstantVisitor($constantName, $value, $modifier),
        ];

        return $this->addVisitorsClassInformationTransfer($classInformationTransfer, $visitors);
    }

    /**
     * @param string $value
     *
     * @throws \SprykerSdk\Integrator\Builder\Exception\LiteralValueParsingException
     *
     * @return \PhpParser\Node\Expr
     */
    protected function parseSingleValue(string $value): Expr
    {
        /** @var array<\PhpParser\Node\Stmt\Expression> $tree */
        $tree = $this->parserFactory->create(ParserFactory::PREFER_PHP7)->parse(sprintf('<?php %s;', $value));

        if ($tree === null) {
            throw new LiteralValueParsingException(sprintf('Value is not valid statement PHP code: `%s`', $value));
        }

        if (count($tree) !== static::SINGLE_EXPRESSION_COUNT) {
            throw new LiteralValueParsingException(sprintf('Value is not single statement PHP code: `%s`', $value));
        }

        return $tree[0]->expr;
    }
}
