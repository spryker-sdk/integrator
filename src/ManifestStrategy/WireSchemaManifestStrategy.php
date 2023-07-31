<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\ManifestStrategy;

class WireSchemaManifestStrategy extends AbstractWireXmlManifestStrategy
{
    /**
     * @return string
     */
    public function getType(): string
    {
        return 'wire-schema';
    }

    /**
     * @return string
     */
    protected function getNewFileTemplate(): string
    {
        return <<<'XML'
            <?xml version="1.0"?>
            <database xmlns="spryker:schema-01" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="spryker:schema-01 https://static.spryker.com/schema-01.xsd">
            </database>
            XML;
    }
}
