<?php 
/**
 * GoMage Seo Booster Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2013-2015 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use/
 * @version      Release: 1.2.0
 * @since        Available since Release 1.0.0
 */

class GoMage_SeoBooster_Block_Catalog_Seo_Sitemap_Additional extends Mage_Catalog_Block_Seo_Sitemap_Abstract
{
    /**
     * Initialize additional links collection
     *
     * @return GoMage_SeoBooster_Block_Catalog_Seo_Sitemap_Additional
     */
    protected function _prepareLayout()
    {
        $additionalLinks = Mage::helper('gomage_seobooster/sitemap')->getAdditionalLinks();
        $collection = new Varien_Data_Collection();
        foreach ($additionalLinks as $linkData) {
            $item = new Varien_Object($linkData);
            $collection->addItem($item);
        }

        $this->setCollection($collection);

        return $this;
    }
}
