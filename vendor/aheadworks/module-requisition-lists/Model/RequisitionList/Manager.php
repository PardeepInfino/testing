<?php
namespace Aheadworks\RequisitionLists\Model\RequisitionList;

use Aheadworks\RequisitionLists\Model\Message\MessageManager;
use Aheadworks\RequisitionLists\Model\Product\Checker\ProhibitedTypeChecker;
use Magento\Framework\Api\DataObjectHelper;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection as OrderCollection;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface;
use Aheadworks\RequisitionLists\Api\RequisitionListManagementInterface;
use Aheadworks\RequisitionLists\Model\RequisitionList\Item\Options\Resolver as OptionsResolver;
use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterfaceFactory;
use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface;
use Aheadworks\RequisitionLists\Model\ResourceModel\Sales\Order\Item\CollectionFactory;
use Aheadworks\RequisitionLists\Model\Product\BuyRequest\Processor as BuyRequestProcessor;

/**
 * Class Manager
 *
 */
class Manager
{
    /**
     * @var RequisitionListManagementInterface
     */
    private $requisitionListService;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var OptionsResolver
     */
    private $resolver;

    /**
     * @var RequisitionListItemInterfaceFactory
     */
    private $requisitionListItemFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var BuyRequestProcessor
     */
    private $buyRequestProcessor;

    /**
     * @var MessageManager
     */
    private $messager;

    /**
     * @var ProhibitedTypeChecker
     */
    private $prohibitedTypeChecker;

    /**
     * @param RequisitionListManagementInterface $requisitionListService
     * @param DataObjectHelper $dataObjectHelper
     * @param OptionsResolver $resolver
     * @param RequisitionListItemInterfaceFactory $requisitionListItemFactory
     * @param CollectionFactory $collectionFactory
     * @param Filter $filter
     * @param BuyRequestProcessor $buyRequestProcessor
     * @param ProhibitedTypeChecker $prohibitedTypeChecker
     * @param MessageManager $messager
     */
    public function __construct(
        RequisitionListManagementInterface $requisitionListService,
        DataObjectHelper $dataObjectHelper,
        OptionsResolver $resolver,
        RequisitionListItemInterfaceFactory $requisitionListItemFactory,
        CollectionFactory $collectionFactory,
        Filter $filter,
        BuyRequestProcessor $buyRequestProcessor,
        ProhibitedTypeChecker $prohibitedTypeChecker,
        MessageManager $messager
    ) {
        $this->requisitionListService = $requisitionListService;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->resolver = $resolver;
        $this->requisitionListItemFactory = $requisitionListItemFactory;
        $this->collectionFactory = $collectionFactory;
        $this->filter = $filter;
        $this->buyRequestProcessor = $buyRequestProcessor;
        $this->messager = $messager;
        $this->prohibitedTypeChecker = $prohibitedTypeChecker;
    }

    /**
     * Add item to list from product page
     *
     * @param RequestInterface $request
     * @param $product
     * @return array|RequisitionListItemInterface
     * @throws LocalizedException
     */
    public function addItemToListFromProductPage($request, $product)
    {
        $params = $request->getParams();

        if ($product->getTypeId() == Grouped::TYPE_CODE && $request->getParam('super_group')) {
            $items = $product->getTypeInstance()->getAssociatedProducts($product);
            $result = $this->addGroupedToListFromProductPage($request, $items);
        } else {
            $result = $this->addItemToList($params);
        }

        return $result;
    }

    /**
     * @param RequestInterface $request
     * @param array $items
     * @return array
     * @throws LocalizedException
     */
    public function addGroupedToListFromProductPage($request, $items)
    {
        $result = [];
        $params = $request->getParams();

        foreach ($items as $item) {
            $params['product'] = $item->getId();
            $superGroup = $params['super_group'] ?? null;
            if ($superGroup && $superGroup[$item->getId()]) {
                $params['qty'] = $superGroup[$item->getId()];
                $result[] = $this->addItemToList($params);
            }
        }

        return $result;
    }

    /**
     * Add item to list from easy reorder
     *
     * @param RequestInterface $request
     * @return RequisitionListItemInterface[]
     * @throws LocalizedException
     */
    public function addItemToListFromEasyReorder($request)
    {
        $listId = $request->getParam(RequisitionListInterface::LIST_ID, null);
        $items = $this->filter
            ->getCollection($this->collectionFactory->create())
            ->getItems();

        return $this->addItemsToList($items, $listId);
    }

    /**
     * @param RequestInterface $request
     * @param OrderCollection $items
     * @return array
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function addItemsToListFromOrder($request, $items)
    {
        $listId = $request->getParam(RequisitionListInterface::LIST_ID, null);

        return $this->addItemsToList($items, $listId);
    }

    /**
     * Add item to list from cart
     *
     * @param array $items
     * @param null $listId
     * @return array
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function addItemToListFromCart($items = [], $listId = null)
    {
        return $this->addItemsToList($items, $listId);
    }

    /**
     * Add item to provided list
     *
     * @param array $params
     * @return RequisitionListItemInterface
     * @throws LocalizedException
     */
    private function addItemToList($params)
    {
        /** @var RequisitionListItemInterface $requestItem */
        $requestItem = $this->requisitionListItemFactory->create();

        $this->dataObjectHelper->populateWithArray(
            $requestItem,
            $this->resolver->resolveParams($params),
            RequisitionListItemInterface::class
        );

        return $this->requisitionListService->addItem($requestItem);
    }

    /**
     * Add items to provided list
     *
     * @param array|OrderCollection $items
     * @param int $listId
     * @return array
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function addItemsToList($items, $listId)
    {
        $result = [];
        $failed = [];

        foreach ($items as $item) {
            if ($item->getParentItem() === null && !$this->prohibitedTypeChecker->isProductProhibited($item)) {
                $params = $this->buyRequestProcessor->prepareForQuoteItem($item);
                $params[RequisitionListItemInterface::LIST_ID] = $listId;

                $result[] = $this->addItemToList($params);
            } else {
                $failed[] = $item->getName();
            }
        }

        if (count($failed)) {
            $this->messager->addErrorMessage(
                __(
                    'Sorry, it’s impossible to add following products to the Requisition List: %1.',
                    implode(',', $failed)
                )
            );
        }

        return $result;
    }
}
