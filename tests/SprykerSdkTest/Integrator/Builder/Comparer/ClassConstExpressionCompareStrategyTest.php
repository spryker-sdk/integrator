<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator\Builder\Comparer;

use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\Error;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\MagicConst\Method;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;
use PHPUnit\Framework\TestCase;
use SprykerSdk\Integrator\Builder\Comparer\ClassConstExpressionCompareStrategy;
use SprykerSdk\Integrator\Builder\Finder\UseStatementsFinder\UseStatementsFinderInterface;

class ClassConstExpressionCompareStrategyTest extends TestCase
{
    /**
     * @return void
     */
    public function testIsEqualShouldReturnFalseWhenNodeNoInstanceOfClassConstFetch(): void
    {
        // Arrange
        $nodeOne = new Method();
        $nodeTwo = new ClassConstFetch(new Name('SomeClass'), 'SOME_CONST');
        $strategy = new ClassConstExpressionCompareStrategy($this->createUseStatementsFinderMock([]));

        // Act
        $resultOne = $strategy->isEqual($nodeOne, $nodeTwo);
        $resultTwo = $strategy->isEqual($nodeTwo, $nodeOne);

        // Assert
        $this->assertFalse($resultOne);
        $this->assertFalse($resultTwo);
    }

    /**
     * @return void
     */
    public function testIsEqualShouldReturnFalseWhenNameIdNotIdentifier(): void
    {
        $nodeOne = new ClassConstFetch(new Error(), 'SOME_CONST');

        $nodeTwo = new ClassConstFetch(new Name('SomeClass'), 'SOME_CONST');
        $strategy = new ClassConstExpressionCompareStrategy($this->createUseStatementsFinderMock([]));

        // Act
        $resultOne = $strategy->isEqual($nodeOne, $nodeTwo);
        $resultTwo = $strategy->isEqual($nodeTwo, $nodeOne);

        // Assert
        $this->assertFalse($resultOne);
        $this->assertFalse($resultTwo);
    }

    /**
     * @return void
     */
    public function testIsEqualShouldReturnFalseWhenNamesNotEquals(): void
    {
        $nodeOne = new ClassConstFetch(new Name('SomeClass'), 'SOME_CONST');

        $nodeTwo = new ClassConstFetch(new Name('OtherClass'), 'SOME_CONST');
        $strategy = new ClassConstExpressionCompareStrategy($this->createUseStatementsFinderMock([]));

        // Act
        $resultOne = $strategy->isEqual($nodeOne, $nodeTwo);
        $resultTwo = $strategy->isEqual($nodeTwo, $nodeOne);

        // Assert
        $this->assertFalse($resultOne);
        $this->assertFalse($resultTwo);
    }

    /**
     * @return void
     */
    public function testIsEqualShouldReturnFalseWhenNameNotInstanceOfName(): void
    {
        // Arrange
        $nodeOne = new ClassConstFetch(new Closure(), 'SOME_CONST');

        $nodeTwo = new ClassConstFetch(new Name('OtherClass'), 'SOME_CONST');
        $strategy = new ClassConstExpressionCompareStrategy($this->createUseStatementsFinderMock([]));

        // Act
        $resultOne = $strategy->isEqual($nodeOne, $nodeTwo);
        $resultTwo = $strategy->isEqual($nodeTwo, $nodeOne);

        // Assert
        $this->assertFalse($resultOne);
        $this->assertFalse($resultTwo);
    }

    /**
     * @return void
     */
    public function testIsEqualShouldReturnTrueWhenFQCNAreEqual(): void
    {
        // Arrange
        $nodeOne = new ClassConstFetch(new FullyQualified('Fully\\Quilified\\Name\\OtherClass'), 'SOME_CONST');

        $nodeTwo = new ClassConstFetch(new FullyQualified('Fully\\Quilified\\Name\\OtherClass'), 'SOME_CONST');
        $strategy = new ClassConstExpressionCompareStrategy($this->createUseStatementsFinderMock([]));

        // Act
        $result = $strategy->isEqual($nodeOne, $nodeTwo);

        // Assert
        $this->assertTrue($result);
    }

    /**
     * @return void
     */
    public function testIsEqualShouldReturnTrueWhenFQCNWithUseStatementAreEqual(): void
    {
        // Arrange
        $nodeOne = new ClassConstFetch(new Name('OtherClass'), 'SOME_CONST');

        $nodeTwo = new ClassConstFetch(new FullyQualified('Fully\\Quilified\\Name\\OtherClass'), 'SOME_CONST');
        $strategy = new ClassConstExpressionCompareStrategy($this->createUseStatementsFinderMock(
            [new Use_([new UseUse(new Name('Fully\\Quilified\\Name\\OtherClass'))])],
        ));

        // Act
        $result = $strategy->isEqual($nodeOne, $nodeTwo);

        // Assert
        $this->assertTrue($result);
    }

    /**
     * @return void
     */
    public function testIsApplicableShouldReturnFalseWhenNodeNoInstanceOfClassConstFetch(): void
    {
        // Arrange
        $nodeOne = new Method();
        $nodeTwo = new ClassConstFetch(new Name('SomeClass'), 'SOME_CONST');
        $strategy = new ClassConstExpressionCompareStrategy($this->createUseStatementsFinderMock([]));

        // Act
        $resultOne = $strategy->isEqual($nodeOne, $nodeTwo);
        $resultTwo = $strategy->isEqual($nodeTwo, $nodeOne);

        // Assert
        $this->assertFalse($resultOne);
        $this->assertFalse($resultTwo);
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
