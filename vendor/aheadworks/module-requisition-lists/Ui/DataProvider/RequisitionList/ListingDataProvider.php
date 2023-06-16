<?php
namespace Aheadworks\RequisitionLists\Ui\DataProvider\RequisitionList;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Class ListingDataProvider
 * @package Aheadworks\RequisitionLists\Ui\DataProvider\RequisitionList
 */
class ListingDataProvider extends DataProvider
{
    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param CustomerSession $customerSession
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        CustomerSession $customerSession,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
        $this->customerSession = $customerSession;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchCriteria()
    {
        if ($customerId = $this->customerSession->getCustomerId()) {
            $filter = $this->filterBuilder
                ->setField(OrderInterface::CUSTOMER_ID)
                ->setValue($customerId)
                ->setConditionType('eq')->create();
            $this->addFilter($filter);
        }
        return parent::getSearchCriteria();
    }
}
