<?php

/**
 * Product:       Xtento_TrackingImport
 * ID:            %!uniqueid!%
 * Last Modified: 2020-10-31T21:17:02+00:00
 * File:          Model/Import/Action/Order/Status.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Model\Import\Action\Order;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Sales\Model\Order\ConfigFactory;
use Magento\Sales\Model\Order\Email\Sender\OrderCommentSender;
use Xtento\TrackingImport\Model\Import\Action\AbstractAction;
use Xtento\TrackingImport\Model\Processor\Mapping\Action\Configuration;
use Xtento\XtCore\Model\System\Config\Source\Order\AllStatuses;

class Status extends AbstractAction
{
    /**
     * @var AllStatuses
     */
    protected $orderStatuses;

    /**
     * @var ConfigFactory
     */
    protected $orderConfigFactory;

    /**
     * @var OrderCommentSender
     */
    protected $orderCommentSender;

    /**
     * Status constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param Configuration $actionConfiguration
     * @param AllStatuses $orderStatuses
     * @param ConfigFactory $orderConfigFactory
     * @param OrderCommentSender $orderCommentSender
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Configuration $actionConfiguration,
        AllStatuses $orderStatuses,
        ConfigFactory $orderConfigFactory,
        OrderCommentSender $orderCommentSender,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->orderStatuses = $orderStatuses;
        $this->orderConfigFactory = $orderConfigFactory;
        $this->orderCommentSender = $orderCommentSender;

        parent::__construct($context, $registry, $actionConfiguration, $resource, $resourceCollection, $data);
    }

    public function update()
    {
        /** @var $order \Magento\Sales\Model\Order */
        $order = $this->getOrder();
        $updateData = $this->getUpdateData();

        if (!isset($updateData['order_status_history_comment'])) {
            $updateData['order_status_history_comment'] = '';
        }

        if ($this->getActionSettingByFieldBoolean('cancel_order', 'enabled')) {
            if ($order->canCancel()) {
                $order->cancel()->save();
                $this->addDebugMessage(
                    __(
                        "Order '%1': Order has been cancelled.",
                        $order->getIncrementId()
                    )
                );
                $this->setHasUpdatedObject(true);
            } else {
                $this->addDebugMessage(
                    __(
                        "Order '%1': Order cannot be cancelled.",
                        $order->getIncrementId()
                    )
                );
            }
        }

        $statusFromFile = isset($updateData['order_status']) ? $updateData['order_status'] : '';
        $statusSet = false;
        if (($this->getActionSettingByFieldBoolean('invoice_create', 'enabled') && $order->canInvoice() && $this->getActionSettingByFieldBoolean('invoice_partial_import', 'enabled'))
            || ($this->getActionSettingByFieldBoolean('shipment_create', 'enabled') && $order->canShip() && $this->getActionSettingByFieldBoolean('shipment_partial_import', 'enabled'))
        ) {
            // Partially imported order. Let's see if we're supposed to change the order status after importing a partial order.
            if ($this->getActionSettingByField('order_status_change_partial', 'value') != '') {
                // "Change status after import" has been set. This value overrides the file import status.
                $statusToChangeTo = $this->getActionSettingByField('order_status_change_partial', 'value');
                if ($order->getStatus() !== $statusToChangeTo) {
                    #$order->setStatus($statusToChangeTo)->save();
                    $statusSet = $this->changeOrderStatus(
                        $order,
                        $statusToChangeTo,
                        $updateData['order_status_history_comment']
                    );
                }
            }
        } else {
            if ($this->getActionSettingByField('order_status_change', 'value') != '') {
                // "Change status after import" has been set. This value overrides the file import status.
                $statusToChangeTo = $this->getActionSettingByField('order_status_change', 'value');
                if ($order->getStatus() !== $statusToChangeTo) {
                    #$order->setStatus($statusToChangeTo)->save();
                    $statusSet = $this->changeOrderStatus(
                        $order,
                        $statusToChangeTo,
                        $updateData['order_status_history_comment']
                    );
                }
            } else {
                if (!empty($statusFromFile) && $this->getActionSettingByFieldBoolean('order_status_file', 'enabled')) {
                    // Status coming from the imported file is not empty. Then let's set this status.
                    $statuses = $this->orderStatuses->toArray();
                    // Make sure the "new" "$status" is a valid Magento status before setting it:
                    if (!in_array($statusFromFile, $statuses)) {
                        $this->addDebugMessage(
                            __(
                                "Attempted to set order status '%1' for order '%2', however that is no status that exists in your Magento installation. Status not changed."
                                ,
                                $statusFromFile,
                                $order->getIncrementId()
                            )
                        );
                    } else {
                        if ($order->getStatus() !== $statusFromFile) {
                            #$order->setStatus($statusFromFile)->save();
                            $statusSet = $this->changeOrderStatus(
                                $order,
                                $statusFromFile,
                                $updateData['order_status_history_comment']
                            );
                            // Alternative for Magento Enterprise Edition:
                            /*
                               $order->addStatusHistoryComment('', $statusFromFile)
                                 ->setIsVisibleOnFront(0)
                                 ->setIsCustomerNotified(0);
                               $order->save();
                             */
                        }
                    }
                }
            }
        }

        if (!$statusSet && !empty($updateData['order_status_history_comment'])) {
            $order->addStatusHistoryComment($updateData['order_status_history_comment'])
                ->setIsCustomerNotified($this->getActionSettingByFieldBoolean('send_order_update_email', 'enabled'))
                ->save();
            $this->addDebugMessage(
                __("Order '%1': Status not updated, order comment added.", $order->getIncrementId())
            );
            $this->setHasUpdatedObject(true);
        } else {
            if ($statusSet) {
                $this->addDebugMessage(
                    __(
                        "Order '%1': Status updated to '%2'. (If the status is different from what you wanted to set, the order state is different already and your order status can't be set anymore)",
                        $order->getIncrementId(),
                        $order->getStatus()
                    )
                );
                $this->setHasUpdatedObject(true);
            } else {
                if ($this->getActionSettingByFieldBoolean('send_order_update_email', 'enabled')) {
                    $order->addStatusHistoryComment($updateData['order_status_history_comment'])
                        ->setIsVisibleOnFront(true)
                        ->setIsCustomerNotified(true);
                    $order->save();
                    $this->setHasUpdatedObject(true);
                }
            }
        }

        if ($this->getActionSettingByFieldBoolean('send_order_update_email', 'enabled')) {
            //$order->sendOrderUpdateEmail(true, $updateData['order_status_history_comment']);
            $this->orderCommentSender->send($order, true, $updateData['order_status_history_comment']);
            /*
             *             $this->state->emulateAreaCode(
                             \Magento\Framework\App\Area::AREA_FRONTEND,
                             [$this->orderCommentSender, 'send'],
                             [$order, true, $updateData['order_status_history_comment']]
                         );
             */
            $this->addDebugMessage(__("Order '%1': Order update email dispatched.", $order->getIncrementId()));
            $this->setHasUpdatedObject(true);
        }

        return true;
    }

    protected function changeOrderStatus($order, $newOrderStatus, $orderComment)
    {
        if ($order->getStatus() == $newOrderStatus) {
            return false;
        }
        $this->setOrderState($order, $newOrderStatus);
        $order->setStatus($newOrderStatus)->save();
        $order->addStatusHistoryComment(
            !empty($orderComment) ? $orderComment : '',
            $order->getStatus()
        )->setIsCustomerNotified($this->getActionSettingByFieldBoolean('send_order_update_email', 'enabled'));

        // Compatibility fix for Amasty_OrderStatus
        /*$statusModel = $this->_registry->registry('amorderstatus_history_status');
        if (($statusModel && $statusModel->getNotifyByEmail()) || $this->_registry->registry(
                'advancedorderstatus_notifications'
            )
        ) {
            $order->sendOrderUpdateEmail();
        }*/
        // End
        $order->save();
        return true;
    }

    protected function setOrderState($order, $newOrderStatus)
    {
        if (!isset($this->orderStates)) {
            $this->orderStates = $this->orderConfigFactory->create()->getStates();
        }
        // Check current order state with priority, as a status can be assigned to multiple states
        foreach ($this->orderStates as $state => $label) {
            if ($state == $order->getState()) {
                foreach ($this->orderConfigFactory->create()->getStateStatuses($state, false) as $status) {
                    if ($status == $newOrderStatus) {
                        return; // Correct state already set
                    }
                }
                break;
            }
        }
        // Status to set is from different state, find state and set
        foreach ($this->orderStates as $state => $label) {
            foreach ($this->orderConfigFactory->create()->getStateStatuses($state, false) as $status) {
                if ($status == $newOrderStatus) {
                    $order->setData('state', $state);
                    return;
                }
            }
        }
    }
}