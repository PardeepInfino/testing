<?php
namespace Aheadworks\RequisitionLists\Ui\Component\RequisitionList\Item\Listing\MassAction\Option;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface;
use Aheadworks\RequisitionLists\Model\Url;

/**
 * Class MoveTo
 * @package Aheadworks\RequisitionLists\Ui\Component\RequisitionList\Item\Listing\MassAction\Option
 */
class MoveTo extends AbstractOption
{
    /**
     * {@inheritDoc}
     */
    protected function prepareUrl($list)
    {
        return $this->urlBuilder->getUrl(
            Url::REQUISITION_LIST_ROUTE . '/moveItem',
            [
                RequisitionListInterface::LIST_ID => $this->listProvider->getRequisitionListId(),
                'move_to_list' => $list->getListId()
            ]
        );
    }
}
