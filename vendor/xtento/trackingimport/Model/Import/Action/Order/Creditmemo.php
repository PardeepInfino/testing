<?php

/**
 * Product:       Xtento_TrackingImport
 * ID:            %!uniqueid!%
 * Last Modified: 2022-08-17T08:01:33+00:00
 * File:          Model/Import/Action/Order/Creditmemo.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Model\Import\Action\Order;

use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Sales\Model\Order\Email\Sender\CreditmemoSender;
use Xtento\TrackingImport\Model\Import\Action\AbstractAction;
use Xtento\TrackingImport\Model\Processor\Mapping\Action\Configuration;
use Magento\Catalog\Model\Product\Type;

class Creditmemo extends AbstractAction
{
    /**
     * @var \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader
     */
    protected $creditmemoLoader;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var CreditmemoSender
     */
    protected $creditmemoSender;

    /**
     * Creditmemo constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param Configuration $actionConfiguration
     * @param \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader
     * @param CreditmemoSender $creditmemoSender
     * @param ProductFactory $productFactory
     * @param ObjectManagerInterface $objectManager
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Configuration $actionConfiguration,
        \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader,
        CreditmemoSender $creditmemoSender,
        ProductFactory $productFactory,
        ObjectManagerInterface $objectManager,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->creditmemoLoader = $creditmemoLoader;
        $this->creditmemoSender = $creditmemoSender;
        $this->productFactory = $productFactory;
        $this->objectManager = $objectManager;

        parent::__construct($context, $registry, $actionConfiguration, $resource, $resourceCollection, $data);
    }

    public function create()
    {
        if ($this->getActionSettingByFieldBoolean('creditmemo_create', 'enabled')) {
            /** @var \Magento\Sales\Model\Order $order */
            $order = $this->getOrder();

            // Prepare items to process
            $itemsToProcess = [];
            $updateData = $this->getUpdateData();
            if (isset($updateData['items']) && !empty($updateData['items'])) {
                foreach ($updateData['items'] as $itemRecord) {
                    $itemRecord['sku'] = strtolower((string)$itemRecord['sku']);
                    if (isset($itemsToProcess[$itemRecord['sku']])) {
                        $itemsToProcess[$itemRecord['sku']]['qty'] = $itemsToProcess[$itemRecord['sku']]['qty'] + $itemRecord['qty'];
                    } else {
                        $itemsToProcess[$itemRecord['sku']]['sku'] = $itemRecord['sku'];
                        $itemsToProcess[$itemRecord['sku']]['qty'] = $itemRecord['qty'];
                    }
                }
            }

            // Create Credit Memo
            if ($order->canCreditmemo()) {
                $refundsToProcess = [];
                $invoices = $order->getInvoiceCollection();
                if ($invoices->count() === 0) {
                    // Offline refund, refund against order
                    $refundsToProcess[] = ['invoice' => false, 'items' => $this->getItemsToRefund($order, 'order', $order->getAllItems(), $itemsToProcess)];
                } else {
                    // Refund against correct invoice
                    foreach ($invoices as $invoice) {
                        if ($invoice->getState() != \Magento\Sales\Model\Order\Invoice::STATE_PAID) {
                            $this->addDebugMessage(
                                __(
                                    "Order '%1', Invoice '%2', invoice is not paid, cannot refund against it.",
                                    $order->getIncrementId(), $invoice->getIncrementId()
                                )
                            );
                            continue;
                        }
                        $itemsToRefund = $this->getItemsToRefund($order, 'invoice', $invoice->getAllItems(), $itemsToProcess);
                        $refundsToProcess[] = ['invoice' => $invoice, 'items' => $itemsToRefund];
                    }
                }
                foreach ($refundsToProcess as $refundToProcess) {
                    $invoice = false;
                    $invoiceId = false;
                    if ($refundToProcess['invoice'] !== false) {
                        $invoice = $refundToProcess['invoice'];
                        $invoiceId = $invoice->getId();
                    }
                    // Start creation
                    $creditmemo = false;
                    $doRefundOrder = true;
                    $data = [];
                    if (array_key_exists('creditmemo_shipping_amount', $updateData)
                        && $updateData['creditmemo_shipping_amount'] != ''
                    ) {
                        $data['shipping_amount'] = $updateData['creditmemo_shipping_amount'];
                    }
                    if (array_key_exists('creditmemo_adjustment_positive', $updateData)
                        && $updateData['creditmemo_adjustment_positive'] != ''
                    ) {
                        $data['adjustment_positive'] = $updateData['creditmemo_adjustment_positive'];
                    }
                    if (array_key_exists('creditmemo_adjustment_negative', $updateData)
                        && $updateData['creditmemo_adjustment_negative'] != ''
                    ) {
                        $data['adjustment_negative'] = $updateData['creditmemo_adjustment_negative'];
                    }
                    //$data['do_offline'] = 1;
                    if ($this->getActionSettingByFieldBoolean('creditmemo_partial_import', 'enabled')) {
                        // Prepare items to invoice for prepareInvoices.. but only if there is SKU info in the import file.
                        $items = $refundToProcess['items'];
                        if (!empty($items)) {
                            $data['items'] = $items;
                            $this->creditmemoLoader->setOrderId($order->getId());
                            if ($invoiceId !== false) {
                                $this->creditmemoLoader->setInvoiceId($invoiceId);
                            }
                            $this->creditmemoLoader->setCreditmemo($data);
                            $creditmemo = $this->creditmemoLoader->load();
                            $this->registry->unregister('current_creditmemo');
                        } else {
                            // We're supposed to import partial credit memos, but no SKUs were found at all. Do not touch credit memo.
                            $doRefundOrder = false;
                            $this->addDebugMessage(
                                __(
                                    "Order '%1', no credit memo was created. Partial credit memo creation enabled, however the items specified in the import file couldn't be found in the order.",
                                    $order->getIncrementId()
                                )
                            );
                        }
                    } else {
                        $this->creditmemoLoader->setOrderId($order->getId());
                        if ($invoiceId !== false) {
                            $this->creditmemoLoader->setInvoiceId($invoiceId);
                        }
                        $this->creditmemoLoader->setCreditmemo($data);
                        $creditmemo = $this->creditmemoLoader->load();
                        $this->registry->unregister('current_creditmemo');
                    }

                    if ($creditmemo && $doRefundOrder) {
                        if (!$creditmemo->isValidGrandTotal()) {
                            // No items or amount to refund against this order/invoice, skip it
                            if ($invoiceId !== false) {
                                $this->addDebugMessage(__("Order '%1', nothing to refund against invoice ID %2, skipping.", $order->getIncrementId(), $invoiceId));
                            } else {
                                $this->addDebugMessage(__("Order '%1', nothing to refund, skipping.", $order->getIncrementId()));
                            }
                            continue;
                        } else {
                            if ($invoiceId !== false) {
                                $this->addDebugMessage(__("Order '%1', found something to refund against invoice ID %2 , proceeding.", $order->getIncrementId(), $invoiceId));
                            } else {
                                $this->addDebugMessage(__("Order '%1', found something to refund, proceeding.", $order->getIncrementId()));
                            }
                        }

                        /** @var \Magento\Sales\Api\CreditmemoManagementInterface $creditmemoManagement */
                        $creditmemoManagement = $this->objectManager->create(
                            'Magento\Sales\Api\CreditmemoManagementInterface'
                        );
                        $refundOffline = true;
                        if ($invoiceId !== false && $invoice->getTransactionId()) {
                            $refundOffline = false;
                        }
                        if ($this->getActionSettingByFieldBoolean('creditmemo_offline', 'enabled')) {
                            $refundOffline = true;
                        }
                        $creditmemoManagement->refund($creditmemo, $refundOffline);

                        if ($this->getActionSettingByFieldBoolean('creditmemo_send_email', 'enabled')) {
                            // Send email async, schedule to be sent
                            if ($this->getActionSettingByFieldBoolean('send_emails_asynchronously', 'enabled')) {
                                $creditmemo->setSendEmail(true);
                                $creditmemo->setEmailSent(null);
                                $creditmemo->save();
                            } else {
                                $this->creditmemoSender->send($creditmemo);
                            }
                            $this->addDebugMessage(
                                __(
                                    "Order '%1' has been refunded and the customer has been notified.",
                                    $order->getIncrementId()
                                )
                            );
                        } else {
                            $this->addDebugMessage(
                                __(
                                    "Order '%1' has been refunded and the customer has NOT been notified.",
                                    $order->getIncrementId()
                                )
                            );
                        }

                        $this->setHasUpdatedObject(true);
                        unset($creditmemo);
                    }
                }
            } else {
                $this->addDebugMessage(
                    __(
                        "Order '%1' has NOT been refunded. Order already refunded or order status not allowing credit memo creation.",
                        $order->getIncrementId()
                    )
                );
            }

            return true;
        }
    }

    /**
     * @param $order
     * @param $itemType
     * @param $items
     * @param $itemsToProcess
     *
     * @return array
     */
    protected function getItemsToRefund($order, $itemType, $items, &$itemsToProcess)
    {
        $itemsToRefund = [];
        foreach ($items as $item) {
            if ($itemType == 'order') {
                $orderItemId = $item->getId();
            } else {
                $orderItemId = $item->getOrderItemId();
            }
            // How should the item be identified in the import file?
            if ($this->getProfileConfiguration()->getProductIdentifier() == 'sku') {
                $orderItemSku = strtolower(trim($item->getSku()));
            } else if ($this->getProfileConfiguration()->getProductIdentifier() == 'order_item_id') {
                $orderItemSku = $orderItemId;
            } else {
                if ($this->getProfileConfiguration()->getProductIdentifier() == 'entity_id') {
                    $orderItemSku = trim($item->getProductId());
                } else {
                    if ($this->getProfileConfiguration()->getProductIdentifier() == 'attribute') {
                        $product = $this->productFactory->create()->load($item->getProductId());
                        if ($product->getId()) {
                            $orderItemSku = strtolower(
                                trim((string)
                                    $product->getData(
                                        $this->getProfileConfiguration()->getProductIdentifierAttributeCode()
                                    )
                                )
                            );
                        } else {
                            $this->addDebugMessage(
                                __(
                                    "Order '%1': Product SKU '%2', product does not exist anymore and cannot be matched for importing.",
                                    $order->getIncrementId(),
                                    $item->getSku()
                                )
                            );
                            continue;
                        }
                    } else {
                        $this->addDebugMessage(
                            __("Order '%1': No method found to match products.", $order->getIncrementId())
                        );
                        return $itemsToRefund;
                    }
                }
            }
            // Item matched?
            if (isset($itemsToProcess[$orderItemSku])) {
                $qtyToProcess = round($itemsToProcess[$orderItemSku]['qty']);
                /** @var \Magento\Sales\Model\Order\Item $orderItem */
                $orderItem = $this->objectManager->create('\Magento\Sales\Model\Order\ItemFactory')->create()->load($orderItemId);
                $maxQty = $orderItem->getQtyToRefund();
                if ($qtyToProcess > $maxQty) {
                    if (($orderItem->getProductType() == Type::TYPE_SIMPLE || $orderItem->getProductType() == Type::TYPE_VIRTUAL)
                        && $orderItem->getParentItem() && $orderItem->getParentItem()->getQtyToRefund() > 0
                    ) {
                        // Has a parent item that must be refunded instead
                        $orderItemId = $orderItem->getParentItem()->getId();
                        $maxQty = $orderItem->getParentItem()->getQtyToRefund();
                        if ($qtyToProcess > $maxQty) {
                            $qty = round($maxQty);
                        } else {
                            $qty = round($qtyToProcess);
                        }
                    } else {
                        $qty = round($maxQty);
                    }
                } else {
                    $qty = round($qtyToProcess);
                }
                if ($qty > 0) {
                    $itemsToProcess[$orderItemSku]['qty'] -= $qty;
                    $itemsToRefund[$orderItemId] = ['qty' => $qty, 'back_to_stock' => true];
                } else {
                    $itemsToRefund[$orderItemId] = ['qty' => 0];
                }
            } else {
                $itemsToRefund[$orderItemId] = ['qty' => 0];
            }
        }
        return $itemsToRefund;
    }
}