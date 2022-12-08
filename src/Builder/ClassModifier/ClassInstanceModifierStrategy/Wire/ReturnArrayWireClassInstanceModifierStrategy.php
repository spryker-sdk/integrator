<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Wire;

use ArrayObject;
use PhpParser\Node\Stmt\ClassMethod;
use SprykerSdk\Integrator\Builder\ClassModifier\AddVisitorsTrait;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Applicable\ApplicableModifierStrategyInterface;
use SprykerSdk\Integrator\Builder\PartialParser\ExpressionPartialParserInterface;
use SprykerSdk\Integrator\Builder\Visitor\AddPluginToPluginListVisitor;
use SprykerSdk\Integrator\Builder\Visitor\AddUseVisitor;
use SprykerSdk\Integrator\Builder\Visitor\PluginPositionResolver\PluginPositionResolverInterface;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;
use SprykerSdk\Integrator\Transfer\ClassMetadataTransfer;

class ReturnArrayWireClassInstanceModifierStrategy implements WireClassInstanceModifierStrategyInterface
{
    use AddVisitorsTrait;

    /**
     * @var \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Applicable\ApplicableModifierStrategyInterface
     */
    protected ApplicableModifierStrategyInterface $applicableCheck;

    /**
     * @var \SprykerSdk\Integrator\Builder\Visitor\PluginPositionResolver\PluginPositionResolverInterface
     */
    protected PluginPositionResolverInterface $pluginPositionResolver;

    /**
     * @var \SprykerSdk\Integrator\Builder\PartialParser\ExpressionPartialParserInterface
     */
    protected ExpressionPartialParserInterface $nodeExpressionPartialParser;

    /**
     * @param \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Applicable\ApplicableModifierStrategyInterface $applicableCheck
     * @param \SprykerSdk\Integrator\Builder\Visitor\PluginPositionResolver\PluginPositionResolverInterface $pluginPositionResolver
     * @param \SprykerSdk\Integrator\Builder\PartialParser\ExpressionPartialParserInterface $nodeExpressionPartialParser
     */
    public function __construct(
        ApplicableModifierStrategyInterface $applicableCheck,
        PluginPositionResolverInterface $pluginPositionResolver,
        ExpressionPartialParserInterface $nodeExpressionPartialParser
    ) {
        $this->applicableCheck = $applicableCheck;
        $this->pluginPositionResolver = $pluginPositionResolver;
        $this->nodeExpressionPartialParser = $nodeExpressionPartialParser;
    }

    /**
     * @param \PhpParser\Node\Stmt\ClassMethod $node
     *
     * @return bool
     */
    public function isApplicable(ClassMethod $node): bool
    {
        return $this->applicableCheck->isApplicable($node);
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $classMetadataTransfer
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function wireClassInstance(
        ClassInformationTransfer $classInformationTransfer,
        ClassMetadataTransfer $classMetadataTransfer
    ): ClassInformationTransfer {
        $visitors = $this->getWireVisitors($classMetadataTransfer);

        return $this->addVisitorsClassInformationTransfer($classInformationTransfer, $visitors);
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $classMetadataTransfer
     *
     * @return array<\PhpParser\NodeVisitorAbstract>
     */
    protected function getWireVisitors(ClassMetadataTransfer $classMetadataTransfer): array
    {
        $visitors = [new AddUseVisitor($classMetadataTransfer->getSourceOrFail())];

        if ($classMetadataTransfer->getIndex() === null) {
            return [...$visitors, new AddPluginToPluginListVisitor($classMetadataTransfer, $this->pluginPositionResolver)];
        }

        $parsedResult = $this->nodeExpressionPartialParser->parse($classMetadataTransfer->getIndex());

        return [
            ...$visitors,
            ...$this->getUseVisitors($parsedResult->getUsedClasses()),
            new AddPluginToPluginListVisitor($classMetadataTransfer, $this->pluginPositionResolver, $parsedResult->getExpression()),
        ];
    }

    /**
     * @param \ArrayObject $usedClasses
     *
     * @return array<\SprykerSdk\Integrator\Builder\Visitor\AddUseVisitor>
     */
    protected function getUseVisitors(ArrayObject $usedClasses): array
    {
        return array_map(
            static fn (string $className): AddUseVisitor => new AddUseVisitor($className),
            iterator_to_array($usedClasses),
        );
    }
}
