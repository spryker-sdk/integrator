<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator\Builder\ArgumentBuilder;

use PhpParser\BuilderFactory;
use PhpParser\Parser;

class ArgumentBuilder implements ArgumentBuilderInterface
{
    /**
     * @var \PhpParser\BuilderFactory
     */
    protected $builderFactory;

    /**
     * @var \PhpParser\Parser
     */
    protected $parser;

    /**
     * @param \PhpParser\BuilderFactory $builderFactory
     * @param \PhpParser\Parser $parser
     */
    public function __construct(BuilderFactory $builderFactory, Parser $parser)
    {
        $this->builderFactory = $builderFactory;
        $this->parser = $parser;
    }

    /**
     * @param array<int, \SprykerSdk\Integrator\Transfer\ClassArgumentMetadataTransfer> $classArgumentMetadataTransfers
     *
     * @return array<\PhpParser\Node\Arg>
     */
    public function getArguments(array $classArgumentMetadataTransfers): array
    {
        $args = [];
        foreach ($classArgumentMetadataTransfers as $classArgumentMetadataTransfer) {
            if ($classArgumentMetadataTransfer->getIsLiteral()) {
                $args = array_merge($args, $this->builderFactory->args([$classArgumentMetadataTransfer->getValue()]));
            }
        }

        return $args;
    }
}
