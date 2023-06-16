<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Visual Merchandiser for Magento 2
 */

namespace Amasty\VisualMerch\Controller\Adminhtml\Product;

use Magento\Framework\Exception\NoSuchEntityException;

abstract class ControllerAbstract extends \Magento\Backend\App\Action
{
    public const ADMIN_RESOURCE = 'Magento_Catalog::categories';

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var \Amasty\VisualMerch\Model\Product\AdminhtmlDataProvider
     */
    protected $dataProvider;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Amasty\VisualMerch\Model\Product\AdminhtmlDataProvider $adminhtmlDataProvider,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->categoryRepository = $categoryRepository;
        $this->categoryFactory = $categoryFactory;
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
        $this->registry = $registry;
        $this->dataProvider = $adminhtmlDataProvider;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * @return \Magento\Catalog\Api\Data\CategoryInterface|\Magento\Catalog\Model\Category
     */
    protected function initCategory()
    {
        $categoryId = $this->resolveCategoryId();
        $storeId = $this->getRequest()->getParam('store', 0);

        try {
            $category = $this->categoryRepository->get($categoryId);
        } catch (NoSuchEntityException $e) {
            $category = $this->categoryFactory->create();
        }

        $category->setStoreId($storeId);
        $this->dataProvider->setCategoryId($categoryId);
        $this->registry->register('category', $category);
        $this->registry->register('current_category', $category);

        return $category;
    }

    /**
     * Resolve Category Id (from get or from post)
     *
     * @return int
     */
    protected function resolveCategoryId()
    {
        $categoryId = $this->getRequest()->getParam('id', 0)
        ?: $this->getRequest()->getParam('entity_id', 0);

        return (int)$categoryId;
    }
}
