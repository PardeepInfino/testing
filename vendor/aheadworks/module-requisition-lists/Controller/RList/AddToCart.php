<?php
namespace Aheadworks\RequisitionLists\Controller\RList;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface;
use Aheadworks\RequisitionLists\Api\RequisitionListRepositoryInterface;
use Aheadworks\RequisitionLists\Api\RequisitionListItemRepositoryInterface;
use Aheadworks\RequisitionLists\Model\Product\DetailProvider\Pool;
use Aheadworks\RequisitionLists\Model\RequisitionList\Provider;
use Aheadworks\RequisitionLists\Model\ResourceModel\RequisitionList\Item\CollectionFactory;
use Aheadworks\RequisitionLists\Api\CartManagementInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class AddToCart
 * @package Aheadworks\RequisitionLists\Controller\RequisitionLists
 */
class AddToCart extends AbstractUpdateItemAction
{
    /**
     * @var CartManagementInterface
     */
    private $cartManager;

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var Pool
     */
    private $pool;

    /**
     * @param Provider $provider
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param ResponseFactory $responseFactory
     * @param RequisitionListRepositoryInterface $requisitionListRepository
     * @param PageFactory $pageFactory
     * @param RequisitionListItemRepositoryInterface $requisitionListItemRepository
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param CartManagementInterface $cartManager
     * @param Session $checkoutSession
     * @param CartRepositoryInterface $cartRepository
     * @param Pool $pool
     */
    public function __construct(
        Provider $provider,
        Context $context,
        CustomerSession $customerSession,
        ResponseFactory $responseFactory,
        RequisitionListRepositoryInterface $requisitionListRepository,
        PageFactory $pageFactory,
        RequisitionListItemRepositoryInterface $requisitionListItemRepository,
        Filter $filter,
        CollectionFactory $collectionFactory,
        CartManagementInterface $cartManager,
        Session $checkoutSession,
        CartRepositoryInterface $cartRepository,
        Pool $pool
    ) {
        parent::__construct(
            $provider,
            $context,
            $customerSession,
            $responseFactory,
            $requisitionListRepository,
            $pageFactory,
            $requisitionListItemRepository,
            $filter,
            $collectionFactory
        );
        $this->cartManager = $cartManager;
        $this->checkoutSession = $checkoutSession;
        $this->cartRepository = $cartRepository;
        $this->pool = $pool;
    }

    /**
     * @inheritDoc
     */
    protected function update($resultRedirect)
    {
        $count = 0;
        $items = $this->getAvailableItems();
        if ($items) {
            $quote = $this->checkoutSession->getQuote();
            if (!$quote->getId()) {
                $this->cartRepository->save($quote);
            }
            $count = $this->cartManager->addItemsToCart($items, $quote->getId());

            if ($count) {
                $resultRedirect->setPath('checkout/cart');
            }
        }

        $this->messageManager->addSuccessMessage(
            __(
                '%1 of %2 item(s) successfully added to the cart.',
                $count,
                count($this->getItems())
            )
        );

        return $resultRedirect;
    }

    /**
     * Retrieve available items
     *
     * @return RequisitionListItemInterface[]
     * @throws LocalizedException
     */
    private function getAvailableItems()
    {
        $availableItems = [];
        $items = $this->getItems();

        /** @var RequisitionListItemInterface $item */
        foreach ($items as $item) {
            try {
                $provider = $this->pool->getProvider($item->getData());
                if ($provider->isAvailableForSite() && $provider->getQtyIsSalable($item->getProductQty())) {
                    $availableItems[] = $item;
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return $availableItems;
    }
}
