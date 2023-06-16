<?php
namespace Aheadworks\RequisitionLists\Ui\Component\RequisitionList\Item\Listing\MassAction\Option;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface;
use Aheadworks\RequisitionLists\Model\Url;

/**
 * Class CopyTo
 * @package Aheadworks\RequisitionLists\Ui\Component\RequisitionList\Item\Listing\MassAction\Option
 */
class CopyTo extends AbstractOption
{
    /**
     * {@inheritDoc}
     */
    protected function prepareUrl($list)
    {
        return $this->urlBuilder->getUrl(
            Url::REQUISITION_LIST_ROUTE . '/copyItem',
            [
                RequisitionListInterface::LIST_ID => $this->listProvider->getRequisitionListId(),
                'copy_to_list' => $list->getListId()
            ]
        );
    }
}
