<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\ClassModifier\ClassConstant;

use PhpParser\Node\Expr;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\ParserFactory;
use SprykerSdk\Integrator\Builder\ClassModifier\AddVisitorsTrait;
use SprykerSdk\Integrator\Builder\Exception\LiteralValueParsingException;
use SprykerSdk\Integrator\Builder\Finder\ClassNodeFinderInterface;
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
     * @var \SprykerSdk\Integrator\Builder\Finder\ClassNodeFinderInterface
     */
    protected $classNodeFinder;

    /**
     * @var \PhpParser\ParserFactory
     */
    private ParserFactory $parserFactory;

    /**
     * @param \SprykerSdk\Integrator\Builder\Finder\ClassNodeFinderInterface $classNodeFinder
     * @param \PhpParser\ParserFactory $parserFactory
     */
    public function __construct(ClassNodeFinderInterface $classNodeFinder, ParserFactory $parserFactory)
    {
        $this->classNodeFinder = $classNodeFinder;
        $this->parserFactory = $parserFactory;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $constantName
     * @param mixed $value
     * @param bool $isLiteral
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function setConstant(ClassInformationTransfer $classInformationTransfer, string $constantName, $value, bool $isLiteral): ClassInformationTransfer
    {
        $parentConstant = $this->getFirstParentConstant($classInformationTransfer, $constantName);

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
        }

        $visitors = [
            new AddConstantVisitor($constantName, $value, $modifier),
        ];

        return $this->addVisitorsClassInformationTransfer($classInformationTransfer, $visitors);
    }

    /**
     * @param ClassInformationTransfer $classInformationTransfer
     * @param string $constantName
     *
     * @return ClassConst|null
     */
    protected function getFirstParentConstant(ClassInformationTransfer $classInformationTransfer, string $constantName): ?ClassConst
    {
        $parentConstant = null;

        do {
            $classInformationTransfer = $classInformationTransfer->getParent();

            if ($classInformationTransfer === null) {
                break;
            }

            $parentConstant = $this->classNodeFinder->findConstantNode($classInformationTransfer, $constantName);
        } while ($parentConstant === null);

        return $parentConstant;
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
