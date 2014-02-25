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
 * Cms sitemap
 *
 * @category   GoMage
 * @package    GoMage_SeoBooster
 * @subpackage Block
 * @author     Roman Bublik <rb@gomage.com>
 */
class GoMage_SeoBooster_Block_Sitemap_Cms extends Mage_Core_Block_Template
{
    /**
     * Initialize cms pages collection
     *
     * @return Mage_Catalog_Block_Seo_Sitemap_Category
     */
    protected function _prepareLayout()
    {
        $collection = $this->_getPagesCollection();
        $this->setCollection($collection);
        return $this;
    }

    protected function _getPagesCollection()
    {
        $storeId = Mage::app()->getStore()->getId();
        $collection = Mage::getModel('cms/page')->getCollection()
            ->addStoreFilter($storeId)
            ->addFieldToFilter('is_active', true)
            ->addFieldToFilter('identifier', array('neq' => Mage_Cms_Model_Page::NOROUTE_PAGE_ID));

        return $collection;
    }

    public function getItemUrl($page)
    {
        $storeId = Mage::app()->getStore()->getId();
        return Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK) . $page->getIdentifier();
    }

}