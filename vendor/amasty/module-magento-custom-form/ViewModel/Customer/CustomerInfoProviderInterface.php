<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Custom Form Base for Magento 2
 */

namespace Amasty\Customform\ViewModel\Customer;

interface CustomerInfoProviderInterface
{
    public function getCustomerId(): int;

    public function getCustomerGroupId(): int;
}
