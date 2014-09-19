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
class GoMage_SeoBooster_Model_Resource_Sitemap_Catalog_Product extends Mage_Sitemap_Model_Resource_Catalog_Product
{
    /**
     * Get category collection array
     *
     * @param unknown_type $storeId
     * @return array
     */
    public function getCollection($storeId)
    {
        $products = array();

        $store = Mage::app()->getStore($storeId);
        /* @var $store Mage_Core_Model_Store */

        if (!$store) {
            return false;
        }

        $urCondions = array(
            'main_table.entity_id=ur.product_id',
            'ur.category_id IS NULL',
            $this->_getWriteAdapter()->quoteInto('ur.store_id=?', $store->getId()),
            $this->_getWriteAdapter()->quoteInto('ur.is_system=?', 1),
        );

        $this->_select = $this->_getWriteAdapter()->select()
            ->from(array('main_table' => $this->getMainTable()), array($this->getIdFieldName()))
            ->join(
                array('w' => $this->getTable('catalog/product_website')),
                'main_table.entity_id=w.product_id',
                array()
            )
            ->where('w.website_id=?', $store->getWebsiteId())
            ->joinLeft(
                array('ur' => $this->getTable('core/url_rewrite')),
                join(' AND ', $urCondions),
                array('url' => 'request_path')
            );

        $sitemapAttribute = Mage::getSingleton('eav/config')
            ->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'exclude_from_sitemap');
        $this->_select->joinLeft(array('sitemap' => $sitemapAttribute->getBackend()->getTable()),
            "main_table.entity_id = sitemap.entity_id AND sitemap.attribute_id = {$sitemapAttribute->getId()}",
            array("exclude_from_sitemap" => "sitemap.value")
        );

        $this->_addFilter($storeId, 'visibility', Mage::getSingleton('catalog/product_visibility')->getVisibleInSiteIds(), 'in');
        $this->_addFilter($storeId, 'status', Mage::getSingleton('catalog/product_status')->getVisibleStatusIds(), 'in');

        $query = $this->_getWriteAdapter()->query($this->_select);

        while ($row = $query->fetch()) {
            $product                     = $this->_prepareProduct($row);
            $products[$product->getId()] = $product;
        }

        return $products;
    }

    /**
     * Prepare catalog object
     *
     * @param array $row
     * @return Varien_Object
     */
    protected function _prepareProduct(array $row)
    {
        $product = new Varien_Object();
        $product->setId($row[$this->getIdFieldName()]);

        $url = !empty($row['url']) ? $row['url'] : 'catalog/product/view/id/' . $product->getId();
        $product->setUrl($url);

        $excludeFromSitemap = !empty($row['exclude_from_sitemap']) ? $row['exclude_from_sitemap'] : 0;
        $product->setExcludeFromSitemap($excludeFromSitemap);

        return $product;
    }

}
