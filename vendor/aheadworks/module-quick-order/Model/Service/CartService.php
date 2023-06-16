<?php
namespace Aheadworks\QuickOrder\Model\Service;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Aheadworks\QuickOrder\Api\ProductListRepositoryInterface;
use Aheadworks\QuickOrder\Api\CartManagementInterface;
use Aheadworks\QuickOrder\Model\Quote\Product\DataProcessor;
use Aheadworks\QuickOrder\Model\Product\AvailabilityChecker;
use Aheadworks\QuickOrder\Api\Data\OperationResultInterface;
use Aheadworks\QuickOrder\Api\Data\OperationResultInterfaceFactory;

/**
 * Class CartService
 *
 * @package Aheadworks\QuickOrder\Model\Service
 */
class CartService implements CartManagementInterface
{
    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var OperationResultInterfaceFactory
     */
    private $operationResultFactory;

    /**
     * @var ProductListRepositoryInterface
     */
    private $listRepository;

    /**
     * @var DataProcessor
     */
    private $dataProcessor;

    /**
     * @var AvailabilityChecker
     */
    private $availabilityChecker;

    /**
     * @param CartRepositoryInterface $cartRepository
     * @param ProductListRepositoryInterface $listRepository
     * @param StoreManagerInterface $storeManager
     * @param OperationResultInterfaceFactory $operationResultFactory
     * @param DataProcessor $dataProcessor
     * @param AvailabilityChecker $availabilityChecker
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        ProductListRepositoryInterface $listRepository,
        StoreManagerInterface $storeManager,
        OperationResultInterfaceFactory $operationResultFactory,
        DataProcessor $dataProcessor,
        AvailabilityChecker $availabilityChecker
    ) {
        $this->cartRepository = $cartRepository;
        $this->listRepository = $listRepository;
        $this->storeManager = $storeManager;
        $this->operationResultFactory = $operationResultFactory;
        $this->dataProcessor = $dataProcessor;
        $this->availabilityChecker = $availabilityChecker;
    }

    /**
     * @inheritdoc
     */
    public function addListToCart($listId, $cartId)
    {
        /** @var OperationResultInterface $operationResult */
        $operationResult = $this->operationResultFactory->create();
        $list = $this->listRepository->get($listId);
        /** @var Quote $quote */
        $quote = $this->cartRepository->get($cartId);
        $websiteId = $this->storeManager->getStore($quote->getStoreId())->getWebsiteId();

        $productListItems = $list->getItems();
        foreach ($productListItems as $index => $item) {
            $messageTitle = __('SKU: %1', $item->getProductSku());
            try {
                $product = $this->dataProcessor->getProduct($item);
                if (!$this->availabilityChecker->isAvailableForSale($product, $item, $websiteId)) {
                    $operationResult->addErrorMessage($messageTitle, __('The product is not available'));
                    continue;
                }
                $buyRequest = $this->dataProcessor->getBuyRequest($item);
                $result = $quote->addProduct($product, $buyRequest);
                if (is_string($result)) {
                    $operationResult->addErrorMessage($messageTitle, $result);
                    continue;
                }
            } catch (\Exception $exception) {
                $operationResult->addErrorMessage($messageTitle, $exception->getMessage());
                continue;
            }

            $operationResult->addSuccessMessage($messageTitle, __('The product has been added to cart'));
            unset($productListItems[$index]);
        }

        //create shipping address in case it wasn't created before
        $quote->getShippingAddress();
        $this->cartRepository->save($quote->collectTotals());
        $list->setItems($productListItems);
        $this->listRepository->save($list);

        return $operationResult;
    }
}
