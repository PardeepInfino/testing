<?php
namespace Aheadworks\QuickOrder\Ui\Component\Listing\Item;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as UiDataProvider;
use Aheadworks\QuickOrder\Api\Data\ProductListItemInterface;
use Aheadworks\QuickOrder\Model\ProductList\SessionManager;

/**
 * Class DataProvider
 *
 * @package Aheadworks\QuickOrder\Ui\Component\Listing\Item
 */
class DataProvider extends UiDataProvider
{
    /**
     * @var SessionManager
     */
    private $sessionManager;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param SessionManager $sessionManager
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
        SessionManager $sessionManager,
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
        $this->sessionManager = $sessionManager;
    }

    /**
     * @inheritdoc
     *
     * @throws CouldNotSaveException
     */
    public function getSearchCriteria()
    {
        $listId = $this->sessionManager->getActiveListIdForCurrentUser();
        if (!$listId) {
            $listId = 'not set';
        }
        $filter = $this->filterBuilder
            ->setField(ProductListItemInterface::LIST_ID)
            ->setValue($listId)
            ->setConditionType('eq')->create();
        $this->addFilter($filter);

        return parent::getSearchCriteria();
    }
}
