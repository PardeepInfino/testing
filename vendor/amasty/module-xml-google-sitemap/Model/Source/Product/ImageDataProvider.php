<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package XML Google® Sitemap for Magento 2
 */

namespace Amasty\XmlSitemap\Model\Source\Product;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Gallery\ReadHandler as GalleryReadHandler;
use Magento\Framework\Data\Collection;
use Magento\Framework\Escaper;

class ImageDataProvider
{
    /**
     * @var GalleryReadHandler
     */
    private $galleryReadHandler;

    /**
     * @var Escaper
     */
    private $escaper;

    public function __construct(
        GalleryReadHandler $galleryReadHandler,
        Escaper $escaper
    ) {
        $this->galleryReadHandler = $galleryReadHandler;
        $this->escaper = $escaper;
    }

    public function getData(Product $product): array
    {
        $this->galleryReadHandler->execute($product);
        $images = $product->getMediaGalleryImages();

        if ($images instanceof Collection) {
            $image = $images->getFirstItem();
            if ($image->getFile()) {
                $imagesData = [
                    'loc' => $this->escaper->escapeUrl($image->getUrl()),
                    'title' => $image->getLabel()
                ];
            }
        }

        return $imagesData ?? [];
    }
}
