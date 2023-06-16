<?php
declare(strict_types=1);

namespace Aheadworks\RequisitionLists\Controller\RList;

use Aheadworks\RequisitionLists\Api\RequisitionListRepositoryInterface;
use Aheadworks\RequisitionLists\Model\Message\MessageManager;
use Aheadworks\RequisitionLists\Model\RequisitionList\Manager as ListManager;
use Aheadworks\RequisitionLists\Model\RequisitionList\Provider;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\Controller\Result\Json as ResultJson;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Add
 * @package Aheadworks\RequisitionLists\Controller\RList
 */
class Add extends AbstractRequisitionListAction
{
    /**
     * @var ListManager
     */
    private $manager;

    /**
     * @var MessageManager
     */
    private $messager;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @param MessageManager $messager
     * @param Provider $provider
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param ResponseFactory $responseFactory
     * @param RequisitionListRepositoryInterface $requisitionListRepository
     * @param ListManager $manager
     * @param Registry $registry
     * @param ProductRepositoryInterface $productRepository
     * @param PageFactory $pageFactory
     */
    public function __construct(
        MessageManager $messager,
        Provider $provider,
        Context $context,
        CustomerSession $customerSession,
        ResponseFactory $responseFactory,
        RequisitionListRepositoryInterface $requisitionListRepository,
        ListManager $manager,
        Registry $registry,
        ProductRepositoryInterface $productRepository,
        PageFactory $pageFactory
    ) {
        parent::__construct(
            $provider,
            $context,
            $customerSession,
            $responseFactory,
            $requisitionListRepository,
            $pageFactory
        );
        $this->manager = $manager;
        $this->registry = $registry;
        $this->messager = $messager;
        $this->productRepository = $productRepository;
    }

    /**
     * Initialize product instance from request data
     *
     * @return \Magento\Catalog\Model\Product|false
     */
    protected function initProduct()
    {
        $productId = (int)$this->getRequest()->getParam('product');
        if ($productId) {
            $storeId = $this->_objectManager->get(
                \Magento\Store\Model\StoreManagerInterface::class
            )->getStore()->getId();
            try {
                return $this->productRepository->getById($productId, false, $storeId);
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        try {
            $product = $this->initProduct();
            if ($product) {
                $item = $this->manager->addItemToListFromProductPage($this->getRequest(), $product);
                $data = $this->resolveListData($item);
                $this->messager->addCombineSuccessMessage(
                    $data
                );
            }
        } catch (\Exception $e) {
            $url = $this->registry->registry('list-url-redirect');

            if ($url) {
                $this->messageManager->addNoticeMessage(
                    $this->_objectManager->get(\Magento\Framework\Escaper::class)->escapeHtml($e->getMessage())
                );

                return $this->goBack($url);
            } else {
                $this->messageManager->addErrorMessage(
                    __('Something wen\'t wrong while adding product to Requisition List.')
                );
            }
        }

        /** @var ResultJson $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        return $resultJson->setData([]);
    }
}
