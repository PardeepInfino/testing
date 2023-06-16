<?php
namespace Aheadworks\RequisitionLists\Model\Order;

use Magento\Framework\App\RequestInterface;
use Magento\Sales\Model\OrderRepository;
/**
 * Class OrderProvider
 */
class OrderProvider
{
    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * OrderProvider constructor.
     * @param OrderRepository $orderRepository
     */
    public function __construct(
        OrderRepository $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param RequestInterface $request
     * @return \Magento\Sales\Api\Data\OrderInterface|null
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function loadCurrentOrder(RequestInterface $request)
    {
        $order = null;
        $orderId = (int)$request->getParam('order_id');

        if ($orderId) {
            $order = $this->orderRepository->get($orderId);
        }

        return $order;
    }
}