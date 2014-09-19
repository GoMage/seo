<?php 
/**
 * GoMage Seo Booster Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2013-2014 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use/
 * @version      Release: 1.1.0
 * @since        Available since Release 1.0.0
 */

class GoMage_SeoBooster_Block_Sitemap_Additional extends Mage_Core_Block_Template
{
    /**
     * Initialize additional pages collection
     *
     * @return Mage_Catalog_Block_Seo_Sitemap_Category
     */
    protected function _prepareLayout()
    {
        $collection = $this->_getAdditionalPagesCollection();
        $this->setCollection($collection);
        return $this;
    }

    protected function _getAdditionalPagesCollection()
    {
        $additionalLinks = Mage::helper('gomage_seobooster/sitemap')->getAdditionalLinks();
        $collection = new Varien_Data_Collection();
        foreach ($additionalLinks as $linkData) {
            $linkData['title'] = $linkData['name'];
            $item = new Varien_Object($linkData);
            $collection->addItem($item);
        }

        return $collection;
    }

    public function getItemUrl($item)
    {
        return $item->getUrl();
    }
}
