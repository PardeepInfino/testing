<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package SEO Toolkit Base for Magento 2
 */

namespace Amasty\SeoToolkitLite\Plugin\CatalogSearch\Controller\Result;

use Amasty\SeoToolkitLite\Helper\Config;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Escaper;
use Magento\Search\Helper\Data as NativeData;
use Magento\Search\Model\QueryFactory;

class Index
{
    /**
     * @var Config
     */
    private $helper;

    /**
     * @var NativeData
     */
    private $searchHelper;
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Escaper
     */
    private $escaper;

    public function __construct(
        Config $helper,
        NativeData $searchHelper,
        RequestInterface $request,
        Escaper $escaper
    ) {
        $this->helper = $helper;
        $this->searchHelper = $searchHelper;
        $this->request = $request;
        $this->escaper = $escaper;
    }

    /**
     * @param $subject
     * @param \Closure $proceed
     * @return mixed
     */
    public function aroundExecute(
        $subject,
        \Closure $proceed
    ) {
        $seoKey = $this->helper->getSeoKey();
        $identifier = trim($this->request->getPathInfo(), '/');
        $identifier = explode('/', $identifier);
        $identifier = array_shift($identifier);
        $query = $this->request->getParam(QueryFactory::QUERY_VAR_NAME);

        if (!$this->request->isForwarded()
            && $this->helper->isSeoUrlsEnabled()
            && $seoKey
            && $seoKey != $identifier
            && $query
        ) {
            $query = $this->escaper->escapeUrl($query);
            // redirect to seo url
            $url = $this->searchHelper->getResultUrl($query);
            $subject->getResponse()->setRedirect($url);
        } else {
            return $proceed();
        }
    }
}
