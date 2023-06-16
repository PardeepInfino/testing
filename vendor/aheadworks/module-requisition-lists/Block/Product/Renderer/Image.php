<?php
namespace Aheadworks\RequisitionLists\Block\Product\Renderer;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Block\Product\ImageBuilder;

/**
 * Class Image
 * @package Aheadworks\RequisitionLists\Block\Product\Renderer
 */
class Image extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Aheadworks_RequisitionLists::product/renderer/image.phtml';

    /**
     * @var ImageBuilder
     */
    private $productImageBuilder;

    /**
     * @param Context $context
     * @param ImageBuilder $productImageBuilder
     * @param array $data
     */
    public function __construct(
        Context $context,
        ImageBuilder $productImageBuilder,
        array $data
    ) {
        parent::__construct($context, $data);
        $this->productImageBuilder = $productImageBuilder;
    }

    /**
     * Get product image
     *
     * @return string
     */
    public function getProductImage()
    {
        if ($product = $this->getProduct()) {
            return $this->productImageBuilder->setProduct($product)
                ->setImageId('category_page_grid')
                ->create()
                ->toHtml();
        }

        return '';
    }
}
