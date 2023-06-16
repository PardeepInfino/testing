<?php
declare(strict_types=1);

namespace Aheadworks\RequisitionLists\Ui\Component\RequisitionList\Item\Listing\MassAction\Option;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface;
use Aheadworks\RequisitionLists\Api\RequisitionListRepositoryInterface;
use Aheadworks\RequisitionLists\Model\RequisitionList\Provider;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Http\Context;
use Magento\Framework\UrlInterface;

/**
 * Class AbstractOption
 * @package Aheadworks\RequisitionLists\Ui\Component\RequisitionList\Item\Listing\MassAction\Option
 */
abstract class AbstractOption implements \JsonSerializable
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var RequisitionListRepositoryInterface
     */
    protected $requisitionListRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Provider
     */
    protected $listProvider;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @param UrlInterface $urlBuilder
     * @param RequisitionListRepositoryInterface $requisitionListRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Provider $listProvider
     * @param Context $context
     */
    public function __construct(
        UrlInterface $urlBuilder,
        RequisitionListRepositoryInterface $requisitionListRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Provider $listProvider,
        Context $context
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->requisitionListRepository = $requisitionListRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->listProvider = $listProvider;
        $this->context = $context;
    }

    /**
     * Get action options
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        if ($this->options === null) {
            $currentListId = $this->listProvider->getRequisitionListId();
            $requisitionLists = $this->requisitionListRepository->getList(
                $this->searchCriteriaBuilder
                    ->addFilter(RequisitionListInterface::CUSTOMER_ID, $this->getCurrentCustomerId())
                    ->addFilter(RequisitionListInterface::LIST_ID, $currentListId, 'neq')
                    ->create()
            )->getItems();

            $options = [];
            foreach ($requisitionLists as $list) {
                $options[] = [
                    'id' => $list->getId(),
                    'type' => 'list_' . $list->getId(),
                    'label' => $list->getName(),
                    'url' => $this->prepareUrl($list)
                ];
            }

            $options[] = [
                'label' => __('Create New Requisition List'),
                'click' => 'this.requisitionListModalForm().openModal()'
            ];

            $this->options = $options;
        }

        return $this->options;
    }

    /**
     * Prepare URL
     *
     * @param RequisitionListInterface $list
     * @return string
     */
    abstract protected function prepareUrl($list);

    /**
     * Retrieve current customer from context
     *
     * @return int
     */
    private function getCurrentCustomerId()
    {
        return (int)$this->context->getValue(RequisitionListInterface::CUSTOMER_ID);
    }
}
