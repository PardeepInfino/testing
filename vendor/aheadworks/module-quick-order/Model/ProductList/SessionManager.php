<?php
namespace Aheadworks\QuickOrder\Model\ProductList;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Model\Session;
use Aheadworks\QuickOrder\Api\ProductListRepositoryInterface;
use Aheadworks\QuickOrder\Api\Data\ProductListInterface;
use Aheadworks\QuickOrder\Api\Data\ProductListInterfaceFactory;

/**
 * Class SessionManager
 *
 * @package Aheadworks\QuickOrder\Model\ProductList
 */
class SessionManager
{
    /**
     * Session list ID mark used for guest users
     */
    const SESSION_LIST_ID = 'aw_qo_list_id';

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var ProductListRepositoryInterface
     */
    private $productListRepository;

    /**
     * @var ProductListInterfaceFactory
     */
    private $productListFactory;

    /**
     * @param Session $customerSession
     * @param ProductListRepositoryInterface $productListRepository
     * @param ProductListInterfaceFactory $productListFactory
     */
    public function __construct(
        Session $customerSession,
        ProductListRepositoryInterface $productListRepository,
        ProductListInterfaceFactory $productListFactory
    ) {
        $this->customerSession = $customerSession;
        $this->productListRepository = $productListRepository;
        $this->productListFactory = $productListFactory;
    }

    /**
     * Get active list ID for current user
     *
     * @param bool $needToCreateNew
     * @return int|null
     * @throws CouldNotSaveException
     */
    public function getActiveListIdForCurrentUser($needToCreateNew = false)
    {
        $listId = null;
        try {
            if ($this->customerSession->getCustomerId()) {
                $list = $this->productListRepository->getByCustomerId($this->customerSession->getCustomerId());
                $listId = $list->getListId();
            } elseif ($this->customerSession->getData(self::SESSION_LIST_ID)) {
                $listId = (int)$this->customerSession->getData(self::SESSION_LIST_ID);
            }
        } catch (\Exception $exception) {
        }

        if (!$listId && $needToCreateNew) {
            $list = $this->createNewListForCurrentUser();
            $listId = $list->getListId();
        }

        return $listId;
    }

    /**
     * Create new list for current user
     *
     * @return ProductListInterface
     * @throws CouldNotSaveException
     */
    public function createNewListForCurrentUser()
    {
        /** @var ProductListInterface $list */
        $list = $this->productListFactory->create();
        $customerId = $this->customerSession->getCustomerId();
        if ($customerId) {
            $list->setCustomerId($this->customerSession->getCustomerId());
        }
        $this->productListRepository->save($list);
        if (!$customerId) {
            $this->customerSession->setData(self::SESSION_LIST_ID, $list->getListId());
        }

        return $list;
    }

    /**
     * Get active list for current user
     *
     * @return ProductListInterface
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     */
    public function getActiveListForCurrentUser()
    {
        $listId = $this->getActiveListIdForCurrentUser();
        return $this->productListRepository->get($listId);
    }

    /**
     * Save list for current user
     *
     * @return bool
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function saveListForCurrentUser()
    {
        $list = $this->getActiveListForCurrentUser();
        $customerId = $this->customerSession->getCustomerId();
        if ($customerId) {
            $list->setCustomerId($this->customerSession->getCustomerId());
            $this->productListRepository->save($list);
        }
        if (!$customerId) {
            $this->customerSession->setData(self::SESSION_LIST_ID, $list->getListId());
        }

        return true;
    }
}
