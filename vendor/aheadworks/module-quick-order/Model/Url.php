<?php
namespace Aheadworks\QuickOrder\Model;

use Magento\Framework\UrlInterface;

/**
 * Class Url
 *
 * @package Aheadworks\QuickOrder\Model
 */
class Url
{
    /**
     * Route for quick order page
     */
    const QUICK_ORDER_ROUTE = 'aw_quick_order';

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Retrieve url to quick order page
     *
     * @return string
     */
    public function getUrlToQuickOrderPage()
    {
        return $this->urlBuilder->getUrl(self::QUICK_ORDER_ROUTE);
    }

    /**
     * Retrieve add to list url
     *
     * @return string
     */
    public function getAddToListUrl()
    {
        return $this->urlBuilder->getUrl(self::QUICK_ORDER_ROUTE . '/quickOrder/addToList');
    }

    /**
     * Retrieve multiple add to list url
     *
     * @return string
     */
    public function getMultipleAddToListUrl()
    {
        return $this->urlBuilder->getUrl(self::QUICK_ORDER_ROUTE . '/quickOrder/addToListMultiple');
    }

    /**
     * Get import file url
     *
     * @return string
     */
    public function getImportFileUrl()
    {
        return $this->urlBuilder->getUrl(self::QUICK_ORDER_ROUTE . '/quickorder/importFile');
    }

    /**
     * Get configure list item url
     *
     * @return string
     */
    public function getConfigureItemUrl()
    {
        return $this->urlBuilder->getUrl(self::QUICK_ORDER_ROUTE . '/quickorder_item/configure');
    }

    /**
     * Get update item option url
     *
     * @return string
     */
    public function getUpdateItemOptionUrl()
    {
        return $this->urlBuilder->getUrl(self::QUICK_ORDER_ROUTE . '/quickorder_item/updateOption');
    }

    /**
     * Get update item qty url
     *
     * @return string
     */
    public function getUpdateItemQtyUrl()
    {
        return $this->urlBuilder->getUrl(self::QUICK_ORDER_ROUTE . '/quickorder_item/updateQty');
    }

    /**
     * Get remove item url
     *
     * @return string
     */
    public function getRemoveItemUrl()
    {
        return $this->urlBuilder->getUrl(self::QUICK_ORDER_ROUTE . '/quickorder_item/remove');
    }

    /**
     * Get link to download sample file for product import
     *
     * @return string
     */
    public function getUrlToDownloadSampleFileForImport()
    {
        return $this->urlBuilder->getUrl(self::QUICK_ORDER_ROUTE . '/quickorder_file/downloadSample');
    }
}
