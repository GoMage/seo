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
class GoMage_SeoBooster_Model_Resource_Analyzer_Page_Collection
    extends GoMage_SeoBooster_Model_Resource_Analyzer_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('gomage_seobooster/analyzer_page');
    }

    public function prepareCollectionForReport()
    {
        $this->getSelect()->joinInner(
            array('cms_page' => $this->getTable('cms/page')),
            'main_table.page_id = cms_page.page_id',
            array('page_name' => 'cms_page.title')
        )->joinLeft(
                array('duplicate_table' => $this->getResource()->getDuplicateTable()),
                'main_table.page_id = duplicate_table.page_id',
                array(
                    'duplicate_entity_id'        => 'duplicate_table.entity_id',
                    'duplicate_name'             => 'duplicate_table.name',
                    'duplicate_meta_description' => 'duplicate_table.meta_description',
                    'duplicate_meta_keyword'     => 'duplicate_table.meta_keyword'
                )
            );

        if ($storeId = $this->_getStoreId()) {
            $this->getSelect()->joinInner(
                array('store_table' => $this->getTable('cms/page_store')),
                'main_table.page_id=store_table.page_id',
                array()
            )->where('store_table.store_id IN (?)', array(0, $storeId))
                ->group('main_table.page_id');
        }

        $this->addFilterToMap('page_id', 'main_table.page_id');
        $this->addFilterToMap('page_name', 'cms_page.title');
        $this->addFilterToMap('duplicate_name', 'duplicate_table.name');
        $this->addFilterToMap('duplicate_meta_description', 'duplicate_table.meta_description');
        $this->addFilterToMap('duplicate_meta_keyword', 'duplicate_table.meta_keyword');

        $this->addFilterToMap('name', 'cms_page.title');
        $this->addFilterToMap('meta_description', 'cms_page.meta_description');
        $this->addFilterToMap('meta_keyword', 'cms_page.meta_keywords');

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
        $field      = $this->_getDuplicateField();
        $productIds = $this->getResource()->getDuplicateValues($entityId, $field);

        if (!$productIds || !is_array($productIds)) {
            return new Varien_Data_Collection();
        }

        $collection = Mage::getModel('cms/page')->getCollection();
        $collection->addFieldToFilter('page_id', array('in' => $productIds));
        $collection->getSelect()->columns(array('meta_keyword' => 'main_table.meta_keywords'));
        $collection->addFilterToMap('meta_keyword', 'main_table.meta_keywords');

        if ($storeId = $this->_getStoreId()) {
            $collection->getSelect()->joinInner(
                array('store_table' => $this->getTable('cms/page_store')),
                'main_table.page_id=store_table.page_id',
                array()
            )->where('store_table.store_id IN (?)', array(0, $storeId))
                ->group('main_table.page_id');
        }
        return $collection;
    }

    public static function getFieldsMap()
    {
        $fields = parent::getFieldsMap();
        unset($fields[GoMage_SeoBooster_Helper_Analyzer::META_TITLE_FIELD]);
        unset($fields[GoMage_SeoBooster_Helper_Analyzer::DESCRIPTION_FIELD]);
        return $fields;
    }
}
