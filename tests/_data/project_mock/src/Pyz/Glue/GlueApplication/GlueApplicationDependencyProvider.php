<?php


namespace Pyz\Glue\GlueApplication;

use Spryker\Glue\GlueApplication\GlueApplicationDependencyProvider as SprykerGlueApplicationDependencyProvider;
use Spryker\Glue\GlueApplication\ResourceRelationshipCollectionInterface;
use Spryker\Glue\TestIntegratorUnwireGlueRelationship\Plugin\UnwireGlueRelationshipPlugin;
use Spryker\Glue\TestIntegratorUnwireGlueRelationship\UnwireGlueRelationshipConfig;

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

        $resourceRelationshipCollection->addRelationship(UnwireGlueRelationshipConfig::TEST_ITEMS_UNWIRE, new UnwireGlueRelationshipPlugin());

        return $resourceRelationshipCollection;
    }
}
