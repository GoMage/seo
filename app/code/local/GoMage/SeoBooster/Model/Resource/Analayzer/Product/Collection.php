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
 * Short description of the class
 *
 * @category   GoMage
 * @package    GoMage_SeoBooster
 * @subpackage Model
 * @author     Roman Bublik <rb@gomage.com>
 */
class GoMage_SeoBooster_Model_Resource_Analayzer_Product_Collection
    extends GoMage_SeoBooster_Model_Resource_Analayzer_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('gomage_seobooster/analayzer_product');
    }

    public function prepareCollectionForReport()
    {
        $nameAttribute = $attribute = Mage::getSingleton('eav/config')
            ->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'name');

        $this->getSelect()->joinInner(
            array('catalog_produt' => $this->getTable('catalog/product')),
            'main_table.product_id = catalog_produt.entity_id',
            array('sku')
        )->joinInner(
            array('catalog_produt_name' => $attribute->getBackend()->getTable()),
            "main_table.product_id = catalog_produt_name.entity_id AND catalog_produt_name.attribute_id = {$nameAttribute->getId()}",
            array('product_name' => 'catalog_produt_name.value')
        )->joinLeft(
            array('duplicate_table' => $this->getResource()->getDuplicateTable()),
            'main_table.product_id = duplicate_table.product_id',
            array(
                'duplicate_entity_id' => 'duplicate_table.entity_id',
                'duplicate_name' => 'duplicate_table.name',
                'duplicate_description' => 'duplicate_table.description',
                'duplicate_meta_title' => 'duplicate_table.meta_title',
                'duplicate_meta_description' => 'duplicate_table.meta_description',
                'duplicate_meta_keyword' => 'duplicate_table.meta_keyword'
            )
        );

        if ($storeId = $this->_getStoreId()) {
            $this->getSelect()->joinInner(
                array('cat_index' => $this->getTable('catalog/category_product_index')),
                "main_table.product_id = cat_index.product_id",
                array()
            )->where("cat_index.store_id = ?", $this->_getStoreId())
            ->group('main_table.product_id');
        }

        return $this;
    }

    protected function _afterLoad()
    {
        foreach ($this->getItems() as $item) {
            foreach ($this->getFieldsMap() as $alias => $field) {
                $value = $item->getData($field);
                if ($value > Mage::helper('gomage_seobooster/analyzer')->getCharsCountLimit($alias)) {
                    $item->setData($alias, array(GoMage_SeoBooster_Model_Analyzer::LONG_ERROR));
                } elseif ($value < Mage::helper('gomage_seobooster/analyzer')->getMinCharsCountLimit($alias)) {
                    $item->setData($alias, array(GoMage_SeoBooster_Model_Analyzer::SHORT_ERROR));
                }

                if ($duplicates = $item->getData('duplicate_'. $alias)) {
                    $duplicates = unserialize($duplicates);
                    if (is_array($duplicates) && count($duplicates) > 1) {
                        $item->setData('duplicate_'. $alias, $duplicates);
                        if ($value = $item->getData($alias)) {
                            $value[] = GoMage_SeoBooster_Model_Analyzer::DUPLICATE_ERROR;
                            $item->setData($alias, $value);
                        } else {
                            $item->setData($alias, array(GoMage_SeoBooster_Model_Analyzer::DUPLICATE_ERROR));
                        }
                    }
                }

                if (!$item->getData($alias)) {
                    $item->setData($alias, GoMage_SeoBooster_Model_Analyzer::MISSING);
                }
            }
        }

        return $this;
    }

    public static function getFieldsMap()
    {
        return array(
            GoMage_SeoBooster_Helper_Analyzer::NAME_FIELD => 'name_chars_count',
            GoMage_SeoBooster_Helper_Analyzer::DESCRIPTION_FIELD => 'description_chars_count',
            GoMage_SeoBooster_Helper_Analyzer::META_TITLE_FIELD => 'meta_title_chars_count',
            GoMage_SeoBooster_Helper_Analyzer::META_DESCRIPTION_FIELD => 'meta_description_chars_count',
            GoMage_SeoBooster_Helper_Analyzer::META_KEYWORD_FIELD => 'meta_keyword_qty'
        );
    }

    protected function _getStoreId()
    {
        if ($storeId = Mage::app()->getRequest()->getParam('store')) {
            return Mage::app()->getStore($storeId)->getId();
        }

        return false;
    }

    /**
     * Return count sql select
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $select = clone $this->getSelect();
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);

        $countSelect = clone $select;
        $countSelect->reset();
        $countSelect->from(array('count_table' => $select), 'COUNT(*)');
        return $countSelect;
    }
}
