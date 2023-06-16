<?php
namespace Aheadworks\RequisitionLists\Model\RequisitionList\Item\Options;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface;
use Aheadworks\RequisitionLists\Model\RequisitionList\Item\Options\Converter as OptionConverter;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Model\StockState;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;

/**
 * Class Resolver
 * @package Aheadworks\RequisitionLists\Model\RequisitionList\Item\Options
 */
class Resolver
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var array
     */
    private $products = [];

    /**
     * @var Converter
     */
    private $optionConverter;

    /**
     * @var Mapper
     */
    private $mapper;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var StockState
     */
    private $stockState;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param Converter $optionConverter
     * @param Registry $registry
     * @param DataObjectFactory $dataObjectFactory
     * @param Mapper $mapper
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        OptionConverter $optionConverter,
        Registry $registry,
        DataObjectFactory $dataObjectFactory,
        Mapper $mapper,
        StockState $stockState
    ) {
        $this->productRepository = $productRepository;
        $this->optionConverter = $optionConverter;
        $this->mapper = $mapper;
        $this->registry = $registry;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->stockState = $stockState;
    }

    /**
     * Resolve Request params for Item factory
     *
     * @param array $requestParams
     * @return array
     * @throws LocalizedException
     */
    public function resolveParams($requestParams)
    {
        /** @var array $itemData */
        $itemData = $this->mapper->mapParams($requestParams);

        $productId = isset($itemData[RequisitionListItemInterface::PRODUCT_ID])
            ? $itemData[RequisitionListItemInterface::PRODUCT_ID]
            : null;
        if (!$productId) {
            throw new LocalizedException(__('We can\'t update Requisition List right now.'));
        }

        return $this->resolveProductData($itemData, $productId, $requestParams);
    }

    /**
     * Resolve and prepare product data for save
     *
     * @param array $itemData
     * @param int $productId
     * @param array $requestParams
     * @return array
     * @throws NoSuchEntityException
     */
    private function resolveProductData($itemData, $productId, $requestParams)
    {
        if (!isset($this->products[$productId])) {
            $this->products[$productId] = $this->productRepository->getById($productId);
        }
        /** @var ProductInterface $productInstance */
        $productInstance = $this->products[$productId];
        $productType = $productInstance->getTypeId();
        $request = $this->dataObjectFactory->create();
        $request->addData($requestParams);
        $listCandidates = $productInstance->getTypeInstance()->prepareForCartAdvanced($request, $productInstance, 'full');
        $state = $this->stockState->checkQtyIncrements(
            $productId,
            $itemData[RequisitionListItemInterface::PRODUCT_QTY],
            $productInstance->getStore()->getWebsiteId()
        );
        $message = $this->resolveMessage($listCandidates, $state);

        if ($message) {
            if ($productInstance->hasOptionsValidationFail()) {
                $redirectUrl = $productInstance->getUrlModel()->getUrl(
                    $productInstance,
                    ['_query' => ['startcustomization' => 1]]
                );
            } else {
                $redirectUrl = $productInstance->getProductUrl();
            }

            $this->registry->register('list-url-redirect', $redirectUrl);

            throw new \Magento\Framework\Exception\LocalizedException(__($message));
        }

        $itemData[RequisitionListItemInterface::PRODUCT_NAME] =
            $productInstance->getName();
        $itemData[RequisitionListItemInterface::PRODUCT_SKU] =
            $productInstance->getSku();
        $itemData[RequisitionListItemInterface::PRODUCT_OPTION] =
            $this->optionConverter->toProductOptionObject($productType, $requestParams, $request);
        $itemData[RequisitionListItemInterface::PRODUCT_TYPE] = $productType;

        return $itemData;
    }

    /**
     * Resolve Message
     *
     * @param array|string $listCandidates
     * @param DataObject $state
     * @return string
     */
    private function resolveMessage($listCandidates, $state)
    {
        $message = is_string($listCandidates) ? $listCandidates : $state->getMessage();

        return $message;
    }
}
