<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Elastic Search Base for Magento 2
 */

namespace Amasty\ElasticSearch\Model\Client;

/**
 * Interface ClientRepositoryInterface
 */
interface ClientRepositoryInterface
{
    /**
     * @return Elasticsearch
     */
    public function get();
}
