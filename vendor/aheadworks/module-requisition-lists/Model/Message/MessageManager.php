<?php
namespace Aheadworks\RequisitionLists\Model\Message;

use Magento\Framework\Event;
use Magento\Framework\Message\CollectionFactory;
use Magento\Framework\Message\ExceptionMessageFactoryInterface;
use Magento\Framework\Message\Factory;
use Magento\Framework\Message\Session;
use Psr\Log\LoggerInterface;

/**
 * Class MessageManager
 */
class MessageManager extends \Magento\Framework\Message\Manager
{
    const ADD_REQUISITION_LIST_SUCCESS_MESSAGE = 'addRequisitionListSuccessMessage';
    const ADD_ITEMS_TO_LIST_SUCCESS_MESSAGE = 'addItemsToListSuccessMessage';

    /**
     * MessageManager constructor.
     * @param Session $session
     * @param Factory $messageFactory
     * @param CollectionFactory $messagesFactory
     * @param Event\ManagerInterface $eventManager
     * @param LoggerInterface $logger
     * @param string $defaultGroup
     * @param ExceptionMessageFactoryInterface|null $exceptionMessageFactory
     */
    public function __construct(
        Session $session,
        Factory $messageFactory,
        CollectionFactory $messagesFactory,
        Event\ManagerInterface $eventManager,
        LoggerInterface $logger,
        $defaultGroup = self::DEFAULT_GROUP,
        ExceptionMessageFactoryInterface $exceptionMessageFactory = null
    ) {
        parent::__construct($session, $messageFactory, $messagesFactory,
            $eventManager, $logger, $defaultGroup, $exceptionMessageFactory);
    }

    /**
     * Adds new combine success message
     *
     * @param string $identifier
     * @param array $listData
     * @param null $group
     * @return MessageManager|\Magento\Framework\Message\ManagerInterface
     */
    public function addCombineSuccessMessage($listData, $identifier = null, $group = null)
    {
        $data = $this->resolveDataMessage($listData);

        if (!$identifier) {
            $identifier = $this->resolveIdentifier($listData['items']);
        }

        return parent::addComplexSuccessMessage($identifier, $data, $group);
    }

    /**
     * Resolve data message
     *
     * @param array $listData
     * @return array
     */
    public function resolveDataMessage($listData)
    {
        if (count($listData['items']) > 1) {
            $listData['count'] = count($listData['items']);
        } else {
            $item = reset($listData['items']);
            $listData['product_name'] = $item->getProductName();
        }

        unset($listData['items']);

        return $listData;
    }

    /**
     * Resolve Identifier
     *
     * @param array $items
     * @return string
     */
    public function resolveIdentifier($items)
    {
        if (count($items) > 1) {
            $identifier = self::ADD_ITEMS_TO_LIST_SUCCESS_MESSAGE;
        } else {
            $identifier = self::ADD_REQUISITION_LIST_SUCCESS_MESSAGE;
        }

        return $identifier;
    }
}