<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Cross Linking for Magento 2
 */

namespace Amasty\CrossLinks\Api;

/**
 * Abstract GiftCard Entrity Interface.
 */
interface LinkInterface
{
    /**
     * Set Link Title
     *
     * @param string $title
     * @return \Amasty\CrossLinks\Api\LinkInterface
     */
    public function setTitle($title);

    /**
     * Get Link Title
     *
     * @return string
     */
    public function getTitle();

    /**
     * @return array
     */
    public function getKeywords();
}
