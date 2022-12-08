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
        $parser = new ExpressionPartialParser((new ParserFactory())->create(ParserFactory::PREFER_PHP7));
        $code = 'some invalid code';

        // Act
        $parserResult = $parser->parse($code);
        $expression = $parserResult->getExpression();

        // Assert
        $this->assertInstanceOf(String_::class, $expression->expr);
        $this->assertSame($code, $expression->expr->value);
    }

    /**
     * @return void
     */
    public function testParseShouldReturnStringExprWhenGlobalConstantSet(): void
    {
        // Arrange
        $parser = new ExpressionPartialParser((new ParserFactory())->create(ParserFactory::PREFER_PHP7));
        $code = 'SOME_VALUE';

        // Act
        $parserResult = $parser->parse($code);
        $expression = $parserResult->getExpression();

        // Assert
        $this->assertInstanceOf(String_::class, $expression->expr);
        $this->assertSame($code, $expression->expr->value);
    }

    /**
     * @return void
     */
    public function testParseShouldReturnExprWhenWellFormedPhpCodeSet(): void
    {
        // Arrange
        $parser = new ExpressionPartialParser((new ParserFactory())->create(ParserFactory::PREFER_PHP7));
        $code = '<?php new \ArrayObject();';

        // Act
        $parserResult = $parser->parse($code);
        $expression = $parserResult->getExpression();

        // Assert
        $this->assertInstanceOf(New_::class, $expression->expr);
        $this->assertSame('ArrayObject', $expression->expr->class->parts[0]);
    }

    /**
     * @return void
     */
    public function testParseShouldReturnExprWithUsedClassesWhenFQCNSet(): void
    {
        // Arrange
        $parser = new ExpressionPartialParser((new ParserFactory())->create(ParserFactory::PREFER_PHP7));
        $code = '\\Spryker\\Shared\\Config\\Config::get(\\Spryker\\Shared\\Log\\LogConstants::LOG_QUEUE_NAME)';
        $dumper = new NodeDumper();

        // Act
        $parserResult = $parser->parse($code);
        $dumpedExpr = $dumper->dump($parserResult->getExpression()->expr);

        // Assert
        $this->assertSame(
            ['Spryker\\Shared\\Config\\Config', 'Spryker\\Shared\\Log\\LogConstants'],
            iterator_to_array($parserResult->getUsedClasses()),
        );

        $this->assertEquals(
            <<<'DUMP'
            Expr_StaticCall(
                class: Name(
                    parts: array(
                        0: Config
                    )
                )
                name: Identifier(
                    name: get
                )
                args: array(
                    0: Arg(
                        name: null
                        value: Expr_ClassConstFetch(
                            class: Name(
                                parts: array(
                                    0: LogConstants
                                )
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
}
