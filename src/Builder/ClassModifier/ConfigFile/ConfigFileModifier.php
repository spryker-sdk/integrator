<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\ClassModifier\ConfigFile;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use SprykerSdk\Integrator\Builder\PartialParser\ExpressionPartialParserInterface;
use SprykerSdk\Integrator\Builder\Visitor\AddArrayItemToEnvConfigVisitor;
use SprykerSdk\Integrator\Transfer\FileInformationTransfer;

class ConfigFileModifier implements ConfigFileModifierInterface
{
    protected ExpressionPartialParserInterface $expressionPartialParser;

    /**
     * @param \SprykerSdk\Integrator\Builder\PartialParser\ExpressionPartialParserInterface $expressionPartialParser
     */
    public function __construct(ExpressionPartialParserInterface $expressionPartialParser)
    {
        $this->expressionPartialParser = $expressionPartialParser;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\FileInformationTransfer $fileInformationTransfer
     * @param string $target
     * @param string $value
     *
     * @return \SprykerSdk\Integrator\Transfer\FileInformationTransfer
     */
    public function addArrayItemToEnvConfig(
        FileInformationTransfer $fileInformationTransfer,
        string $target,
        string $value
    ): FileInformationTransfer {
        $valueStm = $this->expressionPartialParser->parse(sprintf('$var = %s;', $value));

        /** @var \PhpParser\Node\Stmt\Class_|null $node */
        $arrayItem = (new NodeFinder())->findFirst($valueStm, function (Node $node) {
            return $node instanceof ArrayItem;
        });

        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new AddArrayItemToEnvConfigVisitor($target, $arrayItem));

        return $fileInformationTransfer
            ->setTokenTree($nodeTraverser->traverse($fileInformationTransfer->getTokenTree()));
    }
}
