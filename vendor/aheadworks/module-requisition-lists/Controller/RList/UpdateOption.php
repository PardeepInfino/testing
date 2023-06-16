<?php
namespace Aheadworks\RequisitionLists\Controller\RList;

use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface;
use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterfaceFactory;
use Aheadworks\RequisitionLists\Api\RequisitionListItemRepositoryInterface;
use Aheadworks\RequisitionLists\Model\RequisitionList\Item\Options\Converter as OptionConverter;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Class UpdateOption
 * @package Aheadworks\RequisitionLists\Controller\RequisitionLists\Item
 */
class UpdateOption extends Action
{
    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var RequisitionListItemRepositoryInterface
     */
    private $requisitionListItemRepository;

    /**
     * @var OptionConverter
     */
    private $converter;

    /**
     * @var RequisitionListItemInterfaceFactory
     */
    private $listItemFactory;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @param Context $context
     * @param DataObjectProcessor $dataObjectProcessor
     * @param RequisitionListItemRepositoryInterface $requisitionListItemRepository
     * @param OptionConverter $converter
     * @param RequisitionListItemInterfaceFactory $listItemFactory
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        Context $context,
        DataObjectProcessor $dataObjectProcessor,
        RequisitionListItemRepositoryInterface $requisitionListItemRepository,
        OptionConverter $converter,
        RequisitionListItemInterfaceFactory $listItemFactory,
        ProductRepositoryInterface $productRepository
    ) {
        parent::__construct($context);
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->requisitionListItemRepository = $requisitionListItemRepository;
        $this->converter = $converter;
        $this->listItemFactory = $listItemFactory;
        $this->productRepository = $productRepository;
    }

    /**
     * Configure item
     *
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $itemId = $this->getRequest()->getParam(RequisitionListItemInterface::ITEM_ID);
        if (!$itemId) {
            $result = [
                'error' => __('Product list item ID is required'),
            ];

            return $resultJson->setData($result);
        }

        try {
            $buyRequest = $this->getRequest()->getParams();
            $this->updateItemOption($itemId, $buyRequest);
            $result = [
                'success' => true,
            ];
        } catch (\Exception $exception) {
            $result = [
                'error' => $exception->getMessage(),
            ];
        }
        return $resultJson->setData($result);
    }

    /**
     * Prepare buy request and update item option
     *
     * @param int $itemId
     * @param array $buyRequest
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    private function updateItemOption($itemId, $buyRequest)
    {
        /** @var RequisitionListItemInterface $requestItem */
        $requestItem = $this->listItemFactory->create();
        $item = $this->requisitionListItemRepository->get($itemId);
        $product = $this->productRepository->getById($item->getProductId());

        $productOption = $this->converter->toProductOptionObject($item->getProductType(), $buyRequest);
        $requestItem->setProductOption($productOption);
        if (!$requestItem->getProductOption()) {
            if (!$item->getProductOption()) {
                $option = $this->converter->toProductOptionObject($product->getTypeId(), []);
                $item->setProductOption($option);
            }
        } else {
            $item->setProductOption($requestItem->getProductOption());
        }
        $this->requisitionListItemRepository->save($item);
    }
}
