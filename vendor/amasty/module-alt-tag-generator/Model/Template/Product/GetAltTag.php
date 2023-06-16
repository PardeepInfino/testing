<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Alt Tag Generator for Magento 2 (System)
 */

namespace Amasty\AltTagGenerator\Model\Template\Product;

use Amasty\AltTagGenerator\Model\Template\Query\GetByIdInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;

class GetAltTag
{
    /**
     * @var GetAppliedTemplate
     */
    private $getAppliedTemplate;

    /**
     * @var GetByIdInterface
     */
    private $getById;

    /**
     * @var FilterProcessor
     */
    private $filterProcessor;

    public function __construct(
        GetAppliedTemplate $getAppliedTemplate,
        GetByIdInterface $getById,
        FilterProcessor $filterProcessor
    ) {
        $this->getAppliedTemplate = $getAppliedTemplate;
        $this->getById = $getById;
        $this->filterProcessor = $filterProcessor;
    }

    /**
     * @param ProductInterface|Product $product
     * @return string|null
     */
    public function execute(ProductInterface $product): ?string
    {
        $appliedTemplateId = $this->getAppliedTemplate->execute(
            (int) $product->getStoreId(),
            (int) $product->getId()
        );

        if ($appliedTemplateId) {
            $appliedTemplate = $this->getById->execute($appliedTemplateId);
            $result = $this->filterProcessor->execute($appliedTemplate->getTemplate(), $product);
        }

        return $result ?? null;
    }
}
