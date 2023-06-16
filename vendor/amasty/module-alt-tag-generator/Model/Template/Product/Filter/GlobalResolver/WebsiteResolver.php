<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Alt Tag Generator for Magento 2 (System)
 */

namespace Amasty\AltTagGenerator\Model\Template\Product\Filter\GlobalResolver;

use Amasty\AltTagGenerator\Model\Template\Product\Filter\GlobalResolverInterface;
use Magento\Store\Model\StoreManagerInterface;

class WebsiteResolver implements GlobalResolverInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    public function execute(): string
    {
        return $this->storeManager->getWebsite()->getName();
    }
}
