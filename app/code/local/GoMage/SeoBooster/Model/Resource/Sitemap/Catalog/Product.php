<?php
/**
 * GoMage Seo Booster Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2013 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use/
 * @version      Release: 1.0.0
 * @since        Available since Release 1.0.0
 */

/**
 * Sitemap resource product collection model
 *
 * @category   GoMage
 * @package    GoMage_SeoBooster
 * @subpackage Model
 * @author     Roman Bublik <rb@gomage.com>
 */
class GoMage_SeoBooster_Model_Resource_Sitemap_Catalog_Product extends Mage_Sitemap_Model_Resource_Catalog_Product
{
    /**
     * Prepare product
     *
     * @param array $productRow
     * @return Varien_Object
     */
    protected function _prepareProduct(array $productRow)
    {
        $product = parent::_prepareProduct($productRow);
        if (Mage::helper('gomage_seobooster/sitemap')->canIncludeProductImages()) {
            $mediaGallery = $this->_getMediaGallery($product);
            if (count($mediaGallery)) {
                $product->setMediaGallery($mediaGallery);
            }
        }

        return $product;
    }

    /**
     * Return media gallery images
     *
     * @param $product
     * @return array
     */
    protected function _getMediaGallery($product)
    {
        $mediaAttribute = Mage::getSingleton('catalog/product')->getResource()->getAttribute('media_gallery');
        $media = Mage::getResourceSingleton('catalog/product_attribute_backend_media');
        $mediaGallery = $media->loadGallery($product, new Varien_Object(array('attribute' => $mediaAttribute)));

        return $mediaGallery;
    }
}
