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

class GoMage_SeoBooster_Model_Resource_Analyzer_Product_Collection
    extends GoMage_SeoBooster_Model_Resource_Analyzer_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('gomage_seobooster/analyzer_product');
    }

    public function prepareCollectionForReport()
    {
        $nameAttribute = $attribute = Mage::getSingleton('eav/config')
            ->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'name');

        $this->getSelect()->joinInner(
            array('catalog_product' => $this->getTable('catalog/product')),
            'main_table.product_id = catalog_product.entity_id',
            array('sku', 'product_type' => 'catalog_product.type_id')
        )->joinInner(
            array('catalog_product_name' => $attribute->getBackend()->getTable()),
            "main_table.product_id = catalog_product_name.entity_id AND catalog_product_name.attribute_id = {$nameAttribute->getId()}",
            array('product_name' => 'catalog_product_name.value')
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
            )->where("cat_index.store_id = ?", $storeId);

        }

        $this->getSelect()->group('main_table.product_id');
		
        $this->addFilterToMap('product_id', 'main_table.product_id');
        $this->addFilterToMap('product_name', 'catalog_product_name.value');
        $this->addFilterToMap('product_type', 'catalog_product.type_id');
        $this->addFilterToMap('duplicate_name', 'duplicate_table.name');
        $this->addFilterToMap('duplicate_description', 'duplicate_table.description');
        $this->addFilterToMap('duplicate_meta_title', 'duplicate_table.meta_title');
        $this->addFilterToMap('duplicate_meta_description', 'duplicate_table.meta_description');
        $this->addFilterToMap('duplicate_meta_keyword', 'duplicate_table.meta_keyword');

        return $this;
    }

    protected function _afterLoad()
    {
        if (!$this->_addErrors) {
            return $this;
        }

        foreach ($this->getItems() as $item) {
            foreach ($this->getFieldsMap() as $alias => $field) {
                $value = $item->getData($field);
                $minLimit = Mage::helper('gomage_seobooster/analyzer')->getMinCharsCountLimit($alias);
                if ($value == 0) {
                    $item->setData($alias, array(GoMage_SeoBooster_Model_Analyzer::MISSING_ERROR));
                } elseif ($value > Mage::helper('gomage_seobooster/analyzer')->getCharsCountLimit($alias)) {
                    $item->setData($alias, array(GoMage_SeoBooster_Model_Analyzer::LONG_ERROR));
                } elseif ($minLimit != 0 && $value < $minLimit) {
                    $item->setData($alias, array(GoMage_SeoBooster_Model_Analyzer::SHORT_ERROR));
                }

                if ($duplicates = $item->getData('duplicate_'. $alias)) {
                    $duplicates = unserialize($duplicates);
                    if (is_array($duplicates) && count($duplicates) > 1) {
                        $item->setData('duplicate_'. $alias, $duplicates);
                        if ($value = $item->getData($alias)) {
                            if (!is_array($value)) {
                                $value = (array) $value;
                            }
                            $value[] = GoMage_SeoBooster_Model_Analyzer::DUPLICATE_ERROR;
                            $item->setData($alias, $value);
                        } else {
                            $item->setData($alias, array(GoMage_SeoBooster_Model_Analyzer::DUPLICATE_ERROR));
                        }
                    }
                }

                if (!$item->getData($alias)) {
                    $item->setData($alias, GoMage_SeoBooster_Model_Analyzer::RESULT_OK);
                }
            }
        }

        return $this;
    }

    public function getDuplicateCollection($entityId)
    {
        $field = $this->_getDuplicateField();
        $productIds = $this->getResource()->getDuplicateValues($entityId, $field);

        if (!$productIds || !is_array($productIds)) {
            return new Varien_Data_Collection();
        }

        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection->addAttributeToSelect('name');
        if ($field != GoMage_SeoBooster_Helper_Analyzer::NAME_FIELD) {
            $collection->addAttributeToSelect($field);
        }
        $collection->addFieldToFilter('entity_id', array('in' => $productIds));

        if ($storeId = $this->_getStoreId()) {
            $collection->addStoreFilter($storeId);
        }
        return $collection;
    }
}
