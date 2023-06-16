<?php
namespace Aheadworks\RequisitionLists\Controller\RList;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface;
use Aheadworks\RequisitionLists\Api\RequisitionListRepositoryInterface;
use Aheadworks\RequisitionLists\Model\Message\MessageManager;
use Aheadworks\RequisitionLists\Model\Order\OrderProvider;
use Aheadworks\RequisitionLists\Model\RequisitionList\Manager as ListManager;
use Aheadworks\RequisitionLists\Model\RequisitionList\Provider;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class OrderAdd
 */
class OrderAdd extends AbstractRequisitionListAction
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var ListManager
     */
    private $manager;

    /**
     * @var OrderProvider
     */
    private $orderProvider;
    /**
     * @var MessageManager
     */
    private $messager;

    /**
     * OrderAdd constructor.
     * @param MessageManager $messager
     * @param Provider $provider
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param ResponseFactory $responseFactory
     * @param RequisitionListRepositoryInterface $requisitionListRepository
     * @param Registry $registry
     * @param ListManager $manager
     * @param OrderProvider $orderProvider
     * @param PageFactory $pageFactory
     */
    public function __construct(
        MessageManager $messager,
        Provider $provider,
        Context $context,
        CustomerSession $customerSession,
        ResponseFactory $responseFactory,
        RequisitionListRepositoryInterface $requisitionListRepository,
        Registry $registry,
        ListManager $manager,
        OrderProvider $orderProvider,
        PageFactory $pageFactory
    ) {
        parent::__construct($provider, $context, $customerSession, $responseFactory, $requisitionListRepository, $pageFactory);
        $this->registry = $registry;
        $this->manager = $manager;
        $this->orderProvider = $orderProvider;
        $this->messager = $messager;
    }

    /**
     * @return ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $order = $this->orderProvider->loadCurrentOrder($this->getRequest());
            $items = $order->getAllVisibleItems();
            $items = $this->manager->addItemsToListFromOrder($this->getRequest(), $items);
            $data = $this->resolveListData($items);
            $this->messager->addCombineSuccessMessage(
                $data
            );
        } catch (\Exception $e) {
            $url = $this->registry->registry('list-url-redirect');

            if ($url) {
                $this->messageManager->addNoticeMessage(
                    $this->_objectManager->get(\Magento\Framework\Escaper::class)->escapeHtml($e->getMessage())
                );

                return $this->goBack($url);
            } else {
                $this->messageManager->addErrorMessage(
                    __('Something wen\'t wrong while adding products to Requisition List.')
                );
            }
        }

        return $this->getResponse();
    }
}