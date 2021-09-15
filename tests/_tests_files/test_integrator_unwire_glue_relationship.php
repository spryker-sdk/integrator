<?php


namespace Pyz\Glue\GlueApplication;

use Spryker\Glue\GlueApplication\GlueApplicationDependencyProvider as SprykerGlueApplicationDependencyProvider;
use Spryker\Glue\GlueApplication\ResourceRelationshipCollectionInterface;
use Spryker\Glue\TestIntegratorUnwireGlueRelationship\Plugin\UnwireGlueRelationshipPlugin;
use Spryker\Glue\TestIntegratorUnwireGlueRelationship\UnwireGlueRelationshipConfig;
use Spryker\Glue\TestIntegratorWireGlueRelationship\Plugin\WireGlueRelationshipPlugin;
use Spryker\Glue\TestIntegratorWireGlueRelationship\WireGlueRelationshipConfig;

class GlueApplicationDependencyProvider extends SprykerGlueApplicationDependencyProvider
{
    /**
     * @param \Spryker\Glue\GlueApplication\ResourceRelationshipCollectionInterface $resourceRelationshipCollection
     *
     * @return \Spryker\Glue\GlueApplication\ResourceRelationshipCollectionInterface
     *
     * // $resourceRelationshipCollection->addRelationship(UnwireGlueRelationshipConfig::TEST_ITEMS_UNWIRE, new UnwireGlueRelationshipPlugin());
     */
    protected function getResourceRelationshipPlugins(
        ResourceRelationshipCollectionInterface $resourceRelationshipCollection
    ): ResourceRelationshipCollectionInterface {

        $resourceRelationshipCollection->addRelationship(WireGlueRelationshipConfig::TEST_ITEMS_WIRE, new WireGlueRelationshipPlugin());

        return $resourceRelationshipCollection;
    }
}
