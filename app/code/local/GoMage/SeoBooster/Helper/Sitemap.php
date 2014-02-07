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
 * Sitemap helper
 *
 * @category   GoMage
 * @package    GoMage_SeoBooster
 * @subpackage Helper
 * @author     Roman Bublik <rb@gomage.com>
 */
class GoMage_SeoBooster_Helper_Sitemap extends Mage_Core_Helper_Data
{
    /**
     * Return module status
     *
     * @return bool
     */
    protected function _moduleEnabled()
    {
        return Mage::helper('gomage_seobooster')->isEnabled();
    }

    /**
     * Can include product images
     *
     * @return bool
     */
    public function canIncludeProductImages()
    {
        return $this->_moduleEnabled() && Mage::getStoreConfig('sitemap/extended_settings/include_product_images');
    }

    /**
     * Return max images per product
     * @return mixed
     */
    public function getMaxImagesPerProduct()
    {
        return Mage::getStoreConfig('sitemap/extended_settings/max_images_per_product');
    }

    /**
     * Can add product tags
     *
     * @return bool
     */
    public function canAddProductTags()
    {
        return $this->_moduleEnabled() && Mage::getStoreConfig('sitemap/extended_settings/include_product_tags');
    }

    /**
     * Return product tags changefreq
     *
     * @return string
     */
    public function getProductTagsChangefreq()
    {
        return Mage::getStoreConfig('sitemap/extended_settings/paroduct_tags_changefreq');
    }

    /**
     * Return product tags priority
     *
     * @return decimal
     */
    public function getProductTagsPriority()
    {
        return Mage::getStoreConfig('sitemap/extended_settings/paroduct_tags_priority');
    }


}
