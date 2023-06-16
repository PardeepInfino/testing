<?php
namespace Aheadworks\RequisitionLists\Controller\RList;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface;
use Aheadworks\RequisitionLists\Model\Message\MessageManager;
use Aheadworks\RequisitionLists\Model\RequisitionList\Manager as ListManager;
use Aheadworks\RequisitionLists\Api\RequisitionListRepositoryInterface;
use Aheadworks\RequisitionLists\Model\RequisitionList\Provider;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class AddFromReorder
 */
class AddFromReorder extends AbstractRequisitionListAction
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
     * @param MessageManager $messager
     * @param Provider $provider
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param ResponseFactory $responseFactory
     * @param RequisitionListRepositoryInterface $requisitionListRepository
     * @param ListManager $manager
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
        $this->messager = $messager;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('aw_reorder/customer');
        try {
            $items = $this->manager->addItemToListFromEasyReorder($this->getRequest());
            $data = $this->resolveListData($items);
            $this->messager->addCombineSuccessMessage(
                $data
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Something wen\'t wrong while adding product to Requisition List.')
            );
        }

        return $resultRedirect;
    }
}
