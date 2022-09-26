<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Pyz\Yves\ShopApplication;

use Spryker\Yves\TestIntegratorUnwireWidget\Widget\TestUnwireWidget;
use Spryker\Yves\TestIntegratorWireWidget\Widget\TestWidget;

class ShopApplicationDependencyProvider
{
    /**
     * @return array<string>
     */
    protected function getGlobalWidgets(): array
    {
        return [
            TestWidget::class,
        ];
    }
}
