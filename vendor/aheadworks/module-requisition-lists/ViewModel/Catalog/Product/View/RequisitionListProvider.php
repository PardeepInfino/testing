<?php
namespace Aheadworks\RequisitionLists\ViewModel\Catalog\Product\View;

use Aheadworks\RequisitionLists\Model\Product\Checker\ProhibitedTypeChecker;
use Aheadworks\RequisitionLists\Model\Service\CustomerService;
use Aheadworks\RequisitionLists\Model\Url;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class RequisitionListProvider
 */
class RequisitionListProvider implements ArgumentInterface
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var SessionFactory
     */
    private $sessionFactory;

    /**
     * @var Url
     */
    private $urlBuilder;

    /**
     * @var CustomerService
     */
    private $customerService;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ProhibitedTypeChecker
     */
    private $prohibitedTypeChecker;

    /**
     * @param Url $urlBuilder
     * @param CustomerService $customerService
     * @param StoreManagerInterface $storeManager
     * @param SessionFactory $sessionFactory
     * @param ProhibitedTypeChecker $prohibitedTypeChecker
     */
    public function __construct(
        Url $urlBuilder,
        CustomerService $customerService,
        StoreManagerInterface $storeManager,
        SessionFactory $sessionFactory,
        ProhibitedTypeChecker $prohibitedTypeChecker
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->customerService = $customerService;
        $this->storeManager = $storeManager;
        $this->sessionFactory = $sessionFactory;
        $this->prohibitedTypeChecker = $prohibitedTypeChecker;
    }

    /**
     * Get is requisition lists enabled
     */
    public function getIsEnabled()
    {
        return $this->customerService->isActiveForCurrentWebsite(
            $this->storeManager->getWebsite()->getId()
        );
    }

    /**
     * Get is requisition lists enabled
     * @param string $name
     */
    public function getIsEnabledByName($name)
    {
        return $this->customerService->isActiveForCurrentWebsiteByName(
            $name,
            $this->storeManager->getWebsite()->getId()
        );
    }

    /**
     * Get is requisition lists show in order page
     */
    public function getIsShowInOrderPage()
    {
        return $this->customerService->isShowInOrderPageForCurrentWebsite(
            $this->storeManager->getWebsite()->getId()
        );
    }

    /**
     * Get is requisition lists show in catalog
     */
    public function getIsShowInCatalog()
    {
        return $this->customerService->isShowInCatalogForCurrentWebsite(
            $this->storeManager->getWebsite()->getId()
        );
    }

    /**
     * Get add to list URL
     *
     * @return string
     */
    public function getAddToListUrl()
    {
        return $this->urlBuilder->getAddToListUrl();
    }

    /**
     * Get add to list from cart URL
     *
     * @return string
     */
    public function getAddToListFromCartUrl()
    {
        return $this->urlBuilder->getAddToListFromCartUrl();
    }

    /**
     * Get add to list order URL
     *
     * @return string
     */
    public function getAddToListOrderUrl()
    {
        return $this->urlBuilder->getAddToListOrderUrl();
    }

    /**
     * Get url to requisition list page
     *
     * @return string
     */
    public function getUrlToRequisitionListPage()
    {
        return $this->urlBuilder->getUrlToRequisitionListPage();
    }

    /**
     * Get product
     * @param $block
     * @return |null
     */
    public function getProduct($block)
    {
        return $block->getProduct() ?? null;
    }

    /**
     * Get is requisition lists show in order page
     */
    public function getIsShowInCartPage()
    {
        return $this->customerService->isShowInCartPageForCurrentWebsite(
            $this->storeManager->getWebsite()->getId()
        );
    }

    /**
     * Checking customer login status
     *
     * @return bool
     */
    public function isLoggedUserIn()
    {
        return $this->getSession()->isLoggedIn();
    }

    /**
     * Is allowed show requisition list in product list
     *
     * @param ProductInterface $product
     * @return bool
     */
    public function isAllowedInProductList($product)
    {
        return $this->getIsEnabled()
            && $this->getIsShowInCatalog()
            && !$this->prohibitedTypeChecker->isProductProhibited($product);
    }

    /**
     * Get customer session
     *
     * @return \Magento\Customer\Model\Session|\Magento\Customer\Model\Session\Proxy
     */
    public function getSession()
    {
        return $this->session ?: $this->session = $this->sessionFactory->create();
    }
}
