<?php
namespace Aheadworks\RequisitionLists\Model\Product\View;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Aheadworks\RequisitionLists\Api\RequisitionListItemRepositoryInterface;
use Aheadworks\RequisitionLists\Model\Product\View\Processor\RendererComposite;

/**
 * Class Processor
 * @package Aheadworks\RequisitionLists\Model\Product\View
 */
class Processor
{
    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var RequisitionListItemRepositoryInterface
     */
    private $requisitionListItemRepository;

    /**
     * @var DataApplier
     */
    private $productViewDataApplier;

    /**
     * @var RendererComposite
     */
    private $contentRenderer;

    /**
     * @param PageFactory $resultPageFactory
     * @param ProductRepositoryInterface $productRepository
     * @param RequisitionListItemRepositoryInterface $productListItemRepository
     * @param DataApplier $productViewDataApplier
     * @param RendererComposite $contentRenderer
     */
    public function __construct(
        PageFactory $resultPageFactory,
        ProductRepositoryInterface $productRepository,
        RequisitionListItemRepositoryInterface $productListItemRepository,
        DataApplier $productViewDataApplier,
        RendererComposite $contentRenderer
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->productRepository = $productRepository;
        $this->requisitionListItemRepository = $productListItemRepository;
        $this->productViewDataApplier = $productViewDataApplier;
        $this->contentRenderer = $contentRenderer;
    }

    /**
     * Get item configuration
     *
     * It provides content for configuration popup
     *
     * @param int $itemId
     * @param int $storeId
     * @return array
     * @throws NoSuchEntityException
     */
    public function getItemConfiguration($itemId, $storeId)
    {
        $item = $this->requisitionListItemRepository->get($itemId);
        $product = $this->productRepository->getById($item->getProductId(), false, $storeId);
        $this->productViewDataApplier->apply($product, $item);
        $resultPage = $this->resultPageFactory->create();
        $resultPage->addHandle('catalog_product_view');
        $resultPage->addHandle('catalog_product_view_type_' . $product->getTypeId());

        return [
            'title' => __('Configure %1', $product->getName()),
            'content' => $this->contentRenderer->render($resultPage->getLayout())
        ];
    }
}
