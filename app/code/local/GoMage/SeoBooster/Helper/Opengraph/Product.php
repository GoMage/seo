<?php
/**
 * GoMage Seo Booster Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2013-2014 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use/
 * @version      Release: 1.0.0
 * @since        Available since Release 1.0.0
 */

class GoMage_SeoBooster_Helper_Opengraph_Product extends GoMage_SeoBooster_Helper_Opengraph_Abstract
{
    /**
     * Return product entity
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getEntity()
    {
        return Mage::registry('product');
    }

    /**
     * Return product canonical url
     *
     * @return string
     */
    public function getCanonicalUrl()
    {
        $entity = $this->getEntity();
        if ($entity && $entity->getId()) {
            return Mage::helper('gomage_seobooster/product')->getCanonicalUrl($entity);
        }

        return '';
    }

    /**
     * Return product image url
     *
     * @return string
     */
    public function getImage()
    {
        $entity = $this->getEntity();
        if ($entity && $entity->getImage()) {
            return $this->_getImageInfo($entity->getImage(), $entity);
        }

        return false;
    }

    /**
     * Return product images
     *
     * @return array
     */
    public function getImages()
    {
        $product          = $this->getEntity();
        $images           = array();
        $imagesCollection = $product->getMediaGalleryImages();

        foreach ($imagesCollection as $_image) {
            $images[] = $this->_getImageInfo($_image, $product);
        }

        return $images;
    }

    /**
     * Add Product Images
     *
     * @param $ogMetaBlock
     */
    protected function _addImages($ogMetaBlock)
    {
        parent::_addImages($ogMetaBlock);
        $images = $this->getImages();
        foreach ($images as $image) {
            $ogMetaBlock->addItem('og:image', $image['image']);
            $ogMetaBlock->addItem('og:image:url', $image['image']);
            if (isset($image['image_secure']) && $image['image'] != $image['image_secure']) {
                $ogMetaBlock->addItem('og:image:secure_url', $image['image_secure']);
            }
            $ogMetaBlock->addItem('og:image:type', $image['type']);
            $ogMetaBlock->addItem('og:image:width', $image['width']);
            $ogMetaBlock->addItem('og:image:height', $image['height']);
        }
    }
}
