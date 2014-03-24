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

class GoMage_SeoBooster_Model_Resource_Analyzer_Product extends GoMage_SeoBooster_Model_Resource_Analyzer_Abstract
{
    protected $_requiredAttributes = array('name', 'description', 'meta_title', 'meta_description', 'meta_keyword');

    protected $_duplicatesTable = 'gomage_seobooster/analyzer_product_duplicates';

    protected function _construct()
    {
        $this->_init('gomage_seobooster/analyzer_product', 'entity_id');
    }

    public function generateReport()
    {
        $entities = $this->getEntities();
        $duplicates = $this->_getDuplicateEntities($entities);
        $entities = $this->prepareEntities($entities, $duplicates);

        $this->_getWriteAdapter()->truncateTable($this->getMainTable());
        $this->_getWriteAdapter()->truncateTable($this->getDuplicateTable());

        $this->_getWriteAdapter()->insertArray($this->getMainTable(), array(
            'product_id',
            'name_chars_count',
            'description_chars_count',
            'meta_title_chars_count',
            'meta_description_chars_count',
            'meta_keyword_chars_count',
            'meta_keyword_qty'
        ), $entities);

        $this->_getWriteAdapter()->insertArray($this->getDuplicateTable(), array(
            'name',
            'description',
            'meta_title',
            'meta_description',
            'meta_keyword',
            'product_id'
        ), $duplicates);

        $this->_setFlagData(GoMage_SeoBooster_Model_Analyzer::REPORT_PRODUCT_ANALYZER_FLAG_CODE);

        return $this;
    }

    public function getEntities()
    {
        $select = $this->_getReadAdapter()->select();
        $select->from(array('main_table' => $this->getTable('catalog/product')),
            array('product_id' => 'main_table.entity_id'));

        foreach ($this->_requiredAttributes as $attributeCode) {
            $attribute = Mage::getSingleton('eav/config')
                ->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
            $tableAlias = $attributeCode . '_table';
            $select->joinInner(
                array($tableAlias => $attribute->getBackend()->getTable()),
                "main_table.entity_id = {$tableAlias}.entity_id AND {$tableAlias}.attribute_id = {$attribute->getId()}",
                array($attributeCode => $tableAlias.'.value', $attributeCode.'_chars_count' => "CHAR_LENGTH({$tableAlias}.value)")
            );
        }
        $entities = $this->_getReadAdapter()->fetchAll($select);

        return $entities;
    }

    public function prepareEntities($entities, $duplicates)
    {
        foreach ($entities as $_key => &$entity) {
            $isOk = true;
            $entity['meta_keyword_qty'] = $this->_getMetaKeywordsQty($entity['meta_keyword']);
            foreach ($this->_requiredAttributes as $attribute) {
                if (($entity[$attribute. '_chars_count'] > Mage::helper('gomage_seobooster/analyzer')->getCharsCountLimit($attribute))
                    || ($entity[$attribute. '_chars_count'] < Mage::helper('gomage_seobooster/analyzer')->getMinCharsCountLimit($attribute)
                    || ($entity[$attribute. '_chars_count'] == 0))) {
                    $isOk = false;
                }
                if (isset($duplicates[$entity['product_id']]) && $duplicates[$entity['product_id']][$attribute]) {
                    $isOk = false;
                }
                unset($entity[$attribute]);
            }
            if ($isOk === true) {
                unset($entities[$_key]);
            }
        }

        return $entities;
    }

    protected function _getDuplicateEntities($entities)
    {
        $duplicates = array();
        $duplicateProducts = array();

        foreach ($entities as $entity) {
            foreach ($this->_requiredAttributes as $attribute) {
                if (!isset($duplicates[$attribute])) {
                    $duplicates[$attribute] = array();
                }
                if ($entity[$attribute] && !isset($duplicates[$attribute][$entity[$attribute]])) {
                    $duplicates[$attribute][$entity[$attribute]] = array();
                }
                if ($entity[$attribute]) {
                    $duplicates[$attribute][$entity[$attribute]][] = $entity['product_id'];
                }
            }
        }

        $_duplicateTmpl = array();
        foreach ($this->_requiredAttributes as $attribute) {
            $_duplicateTmpl[$attribute] = null;
        }
        foreach ($entities as $entity) {
            foreach ($this->_requiredAttributes as $attribute) {
                if (isset($duplicates[$attribute][$entity[$attribute]]) &&
                    (count($duplicates[$attribute][$entity[$attribute]]) > 1)) {
                    if (!isset($duplicateProducts[$entity['product_id']])) {
                        $duplicateProducts[$entity['product_id']] = $_duplicateTmpl;
                        $duplicateProducts[$entity['product_id']]['product_id'] = $entity['product_id'];
                    }
                    $duplicateProducts[$entity['product_id']][$attribute] = serialize($duplicates[$attribute][$entity[$attribute]]);
                }
            }
        }
        return $duplicateProducts;
    }
}
