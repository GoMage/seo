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
class GoMage_SeoBooster_Model_Resource_Sitemap_Catalog_Category
    extends Mage_Sitemap_Model_Resource_Catalog_Category
{
    /**
     * Get category collection array
     *
     * @param unknown_type $storeId
     * @return array
     */
    public function getCollection($storeId)
    {
        $categories = array();

        $store = Mage::app()->getStore($storeId);
        /* @var $store Mage_Core_Model_Store */

        if (!$store) {
            return false;
        }

        $this->_select = $this->_getWriteAdapter()->select()
            ->from($this->getMainTable())
            ->where($this->getIdFieldName() . '=?', $store->getRootCategoryId());
        $categoryRow   = $this->_getWriteAdapter()->fetchRow($this->_select);

        if (!$categoryRow) {
            return false;
        }

        $urConditions = array(
            'main_table.entity_id=ur.category_id',
            $this->_getWriteAdapter()->quoteInto('ur.store_id=?', $store->getId()),
            'ur.product_id IS NULL',
            $this->_getWriteAdapter()->quoteInto('ur.is_system=?', 1),
        );

        $this->_select = $this->_getWriteAdapter()->select()
            ->from(array('main_table' => $this->getMainTable()), array($this->getIdFieldName()))
            ->joinLeft(
                array('ur' => $this->getTable('core/url_rewrite')),
                join(' AND ', $urConditions),
                array('url' => 'request_path')
            )
            ->where('main_table.path LIKE ?', $categoryRow['path'] . '/%');

        $sitemapAttribute = Mage::getSingleton('eav/config')
            ->getAttribute(Mage_Catalog_Model_Category::ENTITY, 'exclude_from_sitemap');
        $this->_select->joinLeft(array('sitemap' => $sitemapAttribute->getBackend()->getTable()),
            "main_table.entity_id = sitemap.entity_id AND sitemap.attribute_id = {$sitemapAttribute->getId()}",
            array("exclude_from_sitemap" => "sitemap.value")
        );
        $this->_addFilter($storeId, 'is_active', 1);

        $query = $this->_getWriteAdapter()->query($this->_select);
        while ($row = $query->fetch()) {
            $category                       = $this->_prepareCategory($row);
            $categories[$category->getId()] = $category;
        }

        return $categories;
    }

    /**
     * Prepare category
     *
     * @param array $row
     * @return Varien_Object
     */
    protected function _prepareCategory(array $row)
    {
        $category = new Varien_Object();
        $category->setId($row[$this->getIdFieldName()]);
        $url = !empty($row['url']) ? $row['url'] : 'catalog/category/view/id/' . $category->getId();
        $category->setUrl($url);
        $excludeFromSitemap = !empty($row['exclude_from_sitemap']) ? $row['exclude_from_sitemap'] : 0;
        $category->setExcludeFromSitemap($excludeFromSitemap);
        return $category;
    }
}
