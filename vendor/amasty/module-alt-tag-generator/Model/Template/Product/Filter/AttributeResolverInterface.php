<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Alt Tag Generator for Magento 2 (System)
 */

namespace Amasty\AltTagGenerator\Model\Template\Product\Filter;

use Magento\Catalog\Api\Data\ProductInterface;

interface AttributeResolverInterface
{
    public function execute(ProductInterface $product, string $attributeCode): ?string;
}
