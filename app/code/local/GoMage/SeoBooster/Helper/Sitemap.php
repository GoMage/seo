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

    /**
     * Return Additional links
     *
     * @return array
     */
    public function getAdditionalLinks()
    {
        $linksString = Mage::getStoreConfig('sitemap/extended_settings/additional_links');
        $result = array();
        if (!empty($linksString)) {
            $links = explode("\n", $linksString);
            foreach ($links as $_link) {
                $explodedLink = explode(',', $_link);
                if (isset($explodedLink[0])) {
                    $urlPath = ltrim(ltrim($explodedLink[0]), '/');
                    $link = array('url' => Mage::getUrl($urlPath));
                    if (isset($explodedLink[1])) {
                        $link['name'] = ltrim($explodedLink[1]);
                    }
                    $result[] = $link;
                }
            }
        }

        return $result;
    }

    /**
     * Return additional links changefreq
     *
     * @return string
     */
    public function getAdditionalLinksChangefreq()
    {
        return Mage::getStoreConfig('sitemap/extended_settings/additional_links_changefreq');
    }

    /**
     * Return additional links priority
     *
     * @return string
     */
    public function getAdditionalLinksPriority()
    {
        return Mage::getStoreConfig('sitemap/extended_settings/additional_links_priority');
    }

    /**
     * Return link to additional links in sitemap
     *
     * @return string
     */
    public function getLinkToAdditionalUrls()
    {
        return Mage::getUrl('catalog/seo_sitemap/additional/');
    }

    public function canSplitSitemap()
    {
        return Mage::getStoreConfig('sitemap/extended_settings/allow_split_sitemap');
    }

    public function getMaxLinksCount()
    {
        return Mage::getStoreConfig('sitemap/extended_settings/max_links_count');
    }

    public function getMaxFileSize()
    {
        return Mage::getStoreConfig('sitemap/extended_settings/max_file_size');
    }
}
