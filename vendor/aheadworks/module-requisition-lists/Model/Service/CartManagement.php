<?php
namespace Aheadworks\RequisitionLists\Model\Service;

use Aheadworks\RequisitionLists\Api\CartManagementInterface;
use Aheadworks\RequisitionLists\Model\Quote\Product\DataProcessor;
use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;

/**
 * Class CartManagement
 * @package Aheadworks\RequisitionLists\Model\Service
 */
class CartManagement implements CartManagementInterface
{
    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var DataProcessor
     */
    private $dataProcessor;

    /**
     * @param CartRepositoryInterface $cartRepository
     * @param DataProcessor $dataProcessor
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        DataProcessor $dataProcessor
    ) {
        $this->cartRepository = $cartRepository;
        $this->dataProcessor = $dataProcessor;
    }

    /**
     * {@inheritDoc}
     */
    public function addItemsToCart($items, $cartId)
    {
        $count = 0;
        try {
            $cart = $this->cartRepository->get($cartId);
            foreach ($items as $item) {
                $this->addProductToCart($cart, $item) ? $count++ : null;
            }
            $this->cartRepository->save($cart->collectTotals());
        } catch (NoSuchEntityException $noSuchEntityException) {
            return $count;
        }

        return $count;
    }

    /**
     * Add product to shopping cart
     *
     * @param CartInterface $cart
     * @param RequisitionListItemInterface $item
     * @return bool
     */
    private function addProductToCart($cart, $item)
    {
        try {
            $product = $this->dataProcessor->getProduct($item);
            $buyRequest = $this->dataProcessor->getBuyRequest($item);
            $result = $cart->addProduct($product, $buyRequest);
            if (is_string($result)) {
                return false;
            }
        } catch (NoSuchEntityException $noSuchEntityException) {
            return false;
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }
}
