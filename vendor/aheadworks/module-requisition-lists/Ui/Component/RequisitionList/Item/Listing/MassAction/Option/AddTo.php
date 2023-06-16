<?php
namespace Aheadworks\RequisitionLists\Ui\Component\RequisitionList\Item\Listing\MassAction\Option;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface;
use Aheadworks\RequisitionLists\Model\Url;

/**
 * Class AddTo
 * @package Aheadworks\RequisitionLists\Ui\Component\RequisitionList\Item\Listing\MassAction\Option
 */
class AddTo extends AbstractOption
{
    /**
     * {@inheritDoc}
     */
    protected function prepareUrl($list)
    {
        return $this->urlBuilder->getUrl(
            Url::REQUISITION_LIST_ROUTE . '/addFromReorder',
            [
                RequisitionListInterface::LIST_ID => $list->getListId()
            ]
        );
    }
}
