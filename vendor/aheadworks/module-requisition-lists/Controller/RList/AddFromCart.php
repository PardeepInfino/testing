<?php
namespace Aheadworks\RequisitionLists\Controller\RList;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface;
use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface;
use Aheadworks\RequisitionLists\Api\RequisitionListRepositoryInterface;
use Aheadworks\RequisitionLists\Model\Message\MessageManager;
use Aheadworks\RequisitionLists\Model\RequisitionList\Manager as ListManager;
use Aheadworks\RequisitionLists\Model\RequisitionList\Provider;
use Magento\Checkout\Model\Cart as CheckoutCart;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class AddFromCart
 */
class AddFromCart extends AbstractRequisitionListAction
{
    /**
     * @var CheckoutCart
     */
    private $cart;

    /**
     * @var ListManager
     */
    private $manager;

    /**
     * @var Registry
     */
    private $registry;
    /**
     * @var MessageManager
     */
    private $messager;

    /**
     * AddFromCart constructor.
     * @param MessageManager $messager
     * @param Provider $provider
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param ResponseFactory $responseFactory
     * @param Registry $registry
     * @param RequisitionListRepositoryInterface $requisitionListRepository
     * @param CheckoutCart $cart
     * @param ListManager $manager
     * @param PageFactory $pageFactory
     */
    public function __construct(
        MessageManager $messager,
        Provider $provider,
        Context $context,
        CustomerSession $customerSession,
        ResponseFactory $responseFactory,
        Registry $registry,
        RequisitionListRepositoryInterface $requisitionListRepository,
        CheckoutCart $cart,
        ListManager $manager,
        PageFactory $pageFactory
    ) {
        parent::__construct($provider, $context, $customerSession, $responseFactory, $requisitionListRepository, $pageFactory);
        $this->cart = $cart;
        $this->manager = $manager;
        $this->registry = $registry;
        $this->messager = $messager;
    }

    /**
     * @return ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $itemId = (int)$this->getRequest()->getParam('product_id');
            $items = $this->cart->getQuote()->getItemById($itemId);
            $listId = $this->getRequest()->getParam(RequisitionListInterface::LIST_ID, null);

            if (!$items) {
                $items = $this->cart->getQuote()->getAllVisibleItems();
            } elseif(!is_array($items)) {
                $items = [$items];
            }

            if (!$items) {
                throw new LocalizedException(
                    __("The cart item doesn't exist.")
                );
            }

            $items = $this->manager->addItemToListFromCart($items, $listId);
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