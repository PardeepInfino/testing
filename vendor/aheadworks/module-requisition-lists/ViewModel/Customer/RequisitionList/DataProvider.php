<?php
declare(strict_types=1);

namespace Aheadworks\RequisitionLists\ViewModel\Customer\RequisitionList;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface;
use Aheadworks\RequisitionLists\Model\RequisitionListRepository;
use Aheadworks\RequisitionLists\Model\Url;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class DataProvider
 * @package Aheadworks\RequisitionLists\ViewModel\Customer\RequisitionList
 */
class DataProvider implements ArgumentInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var RequisitionListRepository
     */
    private $requisitionListRepository;

    /**
     * @var Url
     */
    private $urlBuilder;

    /**
     * @var FormKey
     */
    private $formKey;

    /**
     * @var RequisitionListInterface
     */
    private $currentList;

    /**
     * @param SerializerInterface $serializer
     * @param RequestInterface $request
     * @param RequisitionListRepository $requisitionListRepository
     * @param FormKey $formKey
     * @param Url $url
     */
    public function __construct(
        SerializerInterface $serializer,
        RequestInterface $request,
        RequisitionListRepository $requisitionListRepository,
        FormKey $formKey,
        Url $url
    ) {
        $this->serializer = $serializer;
        $this->request = $request;
        $this->requisitionListRepository = $requisitionListRepository;
        $this->formKey = $formKey;
        $this->urlBuilder = $url;
    }

    /**
     * Retrieve current requisition list ID
     *
     * @return int
     */
    public function getCurrentRequisitionListId()
    {
        return $this->request->getParam(RequisitionListInterface::LIST_ID, null);
    }

    /**
     * Get current requisition list
     *
     * @return RequisitionListInterface
     * @throws NoSuchEntityException
     */
    public function getCurrentRequisitionList()
    {
        if ($this->currentList === null) {
            $listId = $this->getCurrentRequisitionListId();
            try {
                $this->currentList = $this->requisitionListRepository->get($listId);
            } catch (NoSuchEntityException $e) {
                return null;
            }
        }

        return $this->currentList;
    }

    /**
     * Retrieve current requisition list name
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getCurrentRequisitionListName()
    {
        if ($list = $this->getCurrentRequisitionList()) {
            return $list->getName();
        }

        return '';
    }

    /**
     * Retrieve current requisition list description
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getCurrentRequisitionListDescription()
    {
        if ($list = $this->getCurrentRequisitionList()) {
            return $list->getDescription();
        }

        return '';
    }

    /**
     * Retrieve delete url for requisition list
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->urlBuilder->getDeleteListUrl($this->getCurrentRequisitionListId());
    }

    /**
     * Retrieve redirect url
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->urlBuilder->getUrlToRequisitionListPage();
    }

    /**
     * Retrieve update url
     *
     * @return string
     */
    public function getUpdateUrl()
    {
        return $this->urlBuilder->getUpdateListUrl($this->getCurrentRequisitionListId());
    }

    /**
     * Retrieve form key
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    /**
     * Get config
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getConfig(): string
    {
        $params = [
            'listId'      => $this->getCurrentRequisitionListId(),
            'deleteUrl'   => $this->getDeleteUrl(),
            'name'        => $this->getCurrentRequisitionListName(),
            'description' => $this->getCurrentRequisitionListDescription()
        ];

        return $this->serializer->serialize($params);
    }

    /**
     * Get JS Layout and insert needed values for modal form fields
     *
     * @param AbstractBlock $block
     * @return string
     * @throws NoSuchEntityException
     */
    public function getPreparedJsLayout(AbstractBlock $block): string
    {
        $jsLayout = $this->serializer->unserialize($block->getJsLayout());
        $formFields = &$jsLayout['components']['awRequisitionListParent']['children']
            ['awRequisitionList']['children']['awRequisitionListForm']['children']
            ['fieldset']['children'];

        $formFields[RequisitionListInterface::LIST_ID]['value'] =
            $this->getCurrentRequisitionListId();
        $formFields[RequisitionListInterface::NAME]['default'] =
            $this->getCurrentRequisitionListName();
        $formFields[RequisitionListInterface::DESCRIPTION]['default'] =
            $this->getCurrentRequisitionListDescription();

        return $this->serializer->serialize($jsLayout);
    }
}
