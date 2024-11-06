<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator\Builder\PartialParser;

use PhpParser\Node\Expr\New_;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeDumper;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use SprykerSdk\Integrator\Builder\PartialParser\ExpressionPartialParser;

class ExpressionPartialParserTest extends TestCase
{
    /**
     * @return void
     */
    public function testParseShouldReturnStringExprWhenInvalidPhpCode(): void
    {
        // Arrange
        $parser = new ExpressionPartialParser((new ParserFactory())->createForHostVersion());
        $code = 'some invalid code';

        // Act
        $parserResult = $parser->parse($code);

        // Assert
        $this->assertInstanceOf(String_::class, $parserResult->expr);
        $this->assertSame($code, $parserResult->expr->value);
    }

    /**
     * @return void
     */
    public function testParseShouldReturnStringExprWhenAstIsNull(): void
    {
        // Arrange
        $parser = new ExpressionPartialParser($this->createNullAstParserMock());
        $code = 'new \ArrayObject()';

        // Act
        $parserResult = $parser->parse($code);

        // Assert
        $this->assertInstanceOf(String_::class, $parserResult->expr);
        $this->assertSame($code, $parserResult->expr->value);
    }

    /**
     * @return void
     */
    public function testParseShouldReturnStringExprWhenGlobalConstantSet(): void
    {
        // Arrange
        $parser = new ExpressionPartialParser((new ParserFactory())->createForHostVersion());
        $code = 'SOME_VALUE';

        // Act
        $parserResult = $parser->parse($code);

        // Assert
        $this->assertInstanceOf(String_::class, $parserResult->expr);
        $this->assertSame($code, $parserResult->expr->value);
    }

    /**
     * @return void
     */
    public function testParseShouldReturnExprWhenWellFormedPhpCodeSet(): void
    {
        // Arrange
        $parser = new ExpressionPartialParser((new ParserFactory())->createForHostVersion());
        $code = '<?php new \ArrayObject();';

        // Act
        $parserResult = $parser->parse($code);

        // Assert
        $this->assertInstanceOf(New_::class, $parserResult->expr);
        $this->assertSame('ArrayObject', $parserResult->expr->class->name);
    }

    /**
     * @return void
     */
    public function testParseShouldReturnExprWithUsedClassesWhenFQCNSet(): void
    {
        // Arrange
        $parser = new ExpressionPartialParser((new ParserFactory())->createForHostVersion());
        $code = '\\Spryker\\Shared\\Config\\Config::get(\\Spryker\\Shared\\Log\\LogConstants::LOG_QUEUE_NAME)';
        $dumper = new NodeDumper();

        // Act
        $parserResult = $parser->parse($code);
        $dumpedExpr = $dumper->dump($parserResult->expr, $code);

        // Assert
        $this->assertEquals(
            <<<'DUMP'
            Expr_StaticCall(
                class: Name_FullyQualified(
                    name: Spryker\Shared\Config\Config
                )
                name: Identifier(
                    name: get
                )
                args: array(
                    0: Arg(
                        name: null
                        value: Expr_ClassConstFetch(
                            class: Name_FullyQualified(
                                name: Spryker\Shared\Log\LogConstants
                            )
                            name: Identifier(
                                name: LOG_QUEUE_NAME
                            )
                        )
                        byRef: false
                        unpack: false
                    )
                )
            )
            DUMP,
            $dumpedExpr,
        );
    }

    /**
     * @return \PhpParser\Parser
     */
    protected function createNullAstParserMock(): Parser
    {
        $parser = $this->createMock(Parser::class);
        $parser->method('parse')->willReturn(null);

        return $parser;
    }
}
