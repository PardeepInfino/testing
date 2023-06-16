<?php
namespace Aheadworks\RequisitionLists\Ui\Component\RequisitionList;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\MassAction as UiMassAction;
use Magento\Store\Model\StoreManagerInterface as StoreManager;
use Aheadworks\RequisitionLists\Api\CustomerManagementInterface;

/**
 * Class MassAction
 *
 * @package Aheadworks\RequisitionLists\Ui\Component\RequisitionList
 */
class MassAction extends UiMassAction
{
    const ADD_TO_LIST_ACTION = 'addtolist';

    /**
     * @var StoreManager
     */
    private $storeManager;

    /**
     * @var CustomerManagementInterface
     */
    private $customerManagement;

    /**
     * @param ContextInterface $context
     * @param StoreManager $storeManager
     * @param CustomerManagementInterface $customerManagement
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        StoreManager $storeManager,
        CustomerManagementInterface $customerManagement,
        array $components = [],
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        $this->customerManagement = $customerManagement;
        parent::__construct($context, $components, $data);
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function prepare()
    {
        $isActive = $this->customerManagement->isActiveForCurrentWebsite(
            $this->storeManager->getWebsite()->getId()
        );
        foreach ($this->getChildComponents() as $actionComponent) {
            if ($actionComponent->getName() == self::ADD_TO_LIST_ACTION) {
                $config = $actionComponent->getData('config');
                $config['actionDisable'] = !$isActive;
                $actionComponent->setData('config', $config);
            }
        }
        parent::prepare();
    }
}
