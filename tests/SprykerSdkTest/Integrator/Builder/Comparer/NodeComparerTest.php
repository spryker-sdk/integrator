<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator\Builder\Comparer;

use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\MagicConst\Method;
use PHPUnit\Framework\TestCase;
use SprykerSdk\Integrator\Builder\Comparer\ClassConstExpressionCompareStrategy;
use SprykerSdk\Integrator\Builder\Comparer\NodeComparer;
use SprykerSdk\Integrator\Builder\Comparer\UnsupportedComparerNodeTypeException;
use SprykerSdk\Integrator\Builder\Finder\UseStatementsFinder\UseStatementsFinderInterface;

class NodeComparerTest extends TestCase
{
    /**
     * @return void
     */
    public function testIsEqualShouldReturnTrueIfNodeAreEqual(): void
    {
        // Arrange
        $nodeOne = new ClassConstFetch(new Name('SomeClass'), 'SOME_CONST');
        $nodeTwo = new ClassConstFetch(new Name('SomeClass'), 'SOME_CONST');
        $strategy = new ClassConstExpressionCompareStrategy($this->createUseStatementsFinderMock([]));
        $comparer = new NodeComparer([$strategy]);

        // Act
        $result = $comparer->isEqual($nodeOne, $nodeTwo);

        // Assert
        $this->assertTrue($result);
    }

    /**
     * @return void
     */
    public function testIsEqualShouldThrowExceptionWhenNodesAreNotSupported(): void
    {
        // Arrange && Assert
        $this->expectException(UnsupportedComparerNodeTypeException::class);

        $nodeOne = new Method();
        $nodeTwo = new ClassConstFetch(new Name('SomeClass'), 'SOME_CONST');
        $strategy = new ClassConstExpressionCompareStrategy($this->createUseStatementsFinderMock([]));
        $comparer = new NodeComparer([$strategy]);

        // Act
        $comparer->isEqual($nodeOne, $nodeTwo);
    }

    /**
     * @param array<\PhpParser\Node\Stmt\Use_> $uses
     *
     * @return \SprykerSdk\Integrator\Builder\Finder\UseStatementsFinder\UseStatementsFinderInterface
     */
    protected function createUseStatementsFinderMock(array $uses): UseStatementsFinderInterface
    {
        $useStatementsFinder = $this->createMock(UseStatementsFinderInterface::class);
        $useStatementsFinder->method('findUseStatements')->willReturn($uses);

        return $useStatementsFinder;
    }
}
