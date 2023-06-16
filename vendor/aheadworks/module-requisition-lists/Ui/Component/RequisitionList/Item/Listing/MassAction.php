<?php
declare(strict_types=1);

namespace Aheadworks\RequisitionLists\Ui\Component\RequisitionList\Item\Listing;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface;
use Magento\Ui\Component\MassAction as UiMassAction;

/**
 * Class MassAction
 * @package Aheadworks\RequisitionLists\Ui\Component\RequisitionList\Item\Listing
 */
class MassAction extends UiMassAction
{
    /**
     * {@inheritDoc}
     */
    public function prepare()
    {
        parent::prepare();

        $config = $this->getConfiguration();
        if (isset($config['actions'])) {
            foreach ($config['actions'] as &$action) {
                if (isset($action['url'])) {
                    $action['url'] .= RequisitionListInterface::LIST_ID . '/' . $this->getCurrentRequisitionListId();
                }
            }
        }

        $this->setData('config', $config);
    }

    /**
     * Retrieve current requisition list from request
     *
     * @return string
     */
    private function getCurrentRequisitionListId()
    {
        return $this->getContext()->getRequestParam(RequisitionListInterface::LIST_ID);
    }
}
