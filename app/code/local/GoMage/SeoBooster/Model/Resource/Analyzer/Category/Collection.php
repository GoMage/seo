<?php

/**
 * GoMage Seo Booster Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2013-2015 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use/
 * @version      Release: 1.3.0
 * @since        Available since Release 1.0.0
 */
class GoMage_SeoBooster_Model_Resource_Analyzer_Category_Collection
    extends GoMage_SeoBooster_Model_Resource_Analyzer_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('gomage_seobooster/analyzer_category');
    }

    public function prepareCollectionForReport()
    {
        $nameAttribute = $attribute = Mage::getSingleton('eav/config')
            ->getAttribute(Mage_Catalog_Model_Category::ENTITY, 'name');

        $this->getSelect()->joinInner(
            array('catalog_category' => $this->getTable('catalog/category')),
            'main_table.category_id = catalog_category.entity_id',
            array()
        )->joinInner(
                array('catalog_category_name' => $attribute->getBackend()->getTable()),
                "main_table.category_id = catalog_category_name.entity_id AND catalog_category_name.attribute_id = {$nameAttribute->getId()}",
                array('category_name' => 'catalog_category_name.value')
            )->joinLeft(
                array('duplicate_table' => $this->getResource()->getDuplicateTable()),
                'main_table.category_id = duplicate_table.category_id',
                array(
                    'duplicate_entity_id'        => 'duplicate_table.entity_id',
                    'duplicate_name'             => 'duplicate_table.name',
                    'duplicate_description'      => 'duplicate_table.description',
                    'duplicate_meta_title'       => 'duplicate_table.meta_title',
                    'duplicate_meta_description' => 'duplicate_table.meta_description',
                    'duplicate_meta_keyword'     => 'duplicate_table.meta_keyword'
                )
            );


        $storeId = intval($this->_getStoreId());
        $this->getSelect()
            ->where("catalog_category_name.store_id = ?", $storeId)
            ->group('main_table.category_id');


        $this->addFilterToMap('category_id', 'main_table.category_id');
        $this->addFilterToMap('category_name', 'catalog_category_name.value');
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
                $value    = $item->getData($field);
                $minLimit = Mage::helper('gomage_seobooster/analyzer')->getMinCharsCountLimit($alias);
                if ($value == 0) {
                    $item->setData($alias, array(GoMage_SeoBooster_Model_Analyzer::MISSING_ERROR));
                } elseif ($value > Mage::helper('gomage_seobooster/analyzer')->getCharsCountLimit($alias)) {
                    $item->setData($alias, array(GoMage_SeoBooster_Model_Analyzer::LONG_ERROR));
                } elseif ($minLimit != 0 && $value < $minLimit) {
                    $item->setData($alias, array(GoMage_SeoBooster_Model_Analyzer::SHORT_ERROR));
                }

                if ($duplicates = $item->getData('duplicate_' . $alias)) {
                    $duplicates = unserialize($duplicates);
                    if (is_array($duplicates) && count($duplicates) > 1) {
                        $item->setData('duplicate_' . $alias, $duplicates);
                        if ($value = $item->getData($alias)) {
                            if (!is_array($value)) {
                                $value = (array)$value;
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
        $field       = $this->_getDuplicateField();
        $categoryIds = $this->getResource()->getDuplicateValues($entityId, $field);

        if (!$categoryIds || !is_array($categoryIds)) {
            return new Varien_Data_Collection();
        }

        $collection = Mage::getModel('catalog/category')->getCollection();
        $collection->addAttributeToSelect('name');
        if ($field != GoMage_SeoBooster_Helper_Analyzer::NAME_FIELD) {
            $collection->addAttributeToSelect($field);
        }
        $collection->addFieldToFilter('entity_id', array('in' => $categoryIds));
        if ($storeId = $this->_getStoreId()) {
            $collection
                ->setProductStoreId($storeId)
                ->setStoreId($storeId);
        }
        return $collection;
    }
}
