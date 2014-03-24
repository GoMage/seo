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

class GoMage_SeoBooster_Model_Resource_Analyzer_Page extends GoMage_SeoBooster_Model_Resource_Analyzer_Abstract
{
    protected $_requiredAttributes = array('title', 'meta_description', 'meta_keywords');

    protected $_duplicatesTable = 'gomage_seobooster/analyzer_page_duplicates';

    protected function _construct()
    {
        $this->_init('gomage_seobooster/analyzer_page', 'entity_id');
    }

    public function generateReport()
    {
        $entities = $this->getEntities();
        $duplicates = $this->_getDuplicateEntities($entities);
        $entities = $this->prepareEntities($entities, $duplicates);

        $this->_getWriteAdapter()->truncateTable($this->getMainTable());
        $this->_getWriteAdapter()->truncateTable($this->getDuplicateTable());

        $this->_getWriteAdapter()->insertArray($this->getMainTable(), array(
            'page_id',
            'name_chars_count',
            'meta_description_chars_count',
            'meta_keyword_chars_count',
            'meta_keyword_qty'
        ), $entities);

        $this->_getWriteAdapter()->insertArray($this->getDuplicateTable(), array(
            'name',
            'meta_description',
            'meta_keyword',
            'page_id'
        ), $duplicates);

        $this->_setFlagData(GoMage_SeoBooster_Model_Analyzer::REPORT_PAGE_ANALYZER_FLAG_CODE);

        return $this;
    }

    public function getEntities()
    {
        $columns = array();
        foreach ($this->_requiredAttributes as $attributeCode) {
            $columns[$attributeCode] = 'main_table.'.$attributeCode;
            $columns[$attributeCode.'_chars_count'] = 'CHAR_LENGTH(main_table.'.$attributeCode.')';
        }
        $select = $this->_getReadAdapter()->select();
        $select->from(array('main_table' => $this->getTable('cms/page')), array_merge(
            array('page_id' => 'main_table.page_id'),
            $columns
        ));

        $entities = $this->_getReadAdapter()->fetchAll($select);

        return $entities;
    }

    public function prepareEntities($entities, $duplicates)
    {
        foreach ($entities as $_key => &$entity) {
            $isOk = true;
            $entity['meta_keyword_qty'] = $this->_getMetaKeywordsQty($entity['meta_keywords']);
            foreach ($this->_requiredAttributes as $attribute) {
                $alias = ($attribute == 'meta_keywords' ? 'meta_keyword' : $attribute == 'title' ? 'name' : $attribute);
                if (($entity[$attribute. '_chars_count'] > Mage::helper('gomage_seobooster/analyzer')->getCharsCountLimit($alias))
                    || ($entity[$attribute. '_chars_count'] < Mage::helper('gomage_seobooster/analyzer')->getMinCharsCountLimit($alias)
                        || ($entity[$attribute. '_chars_count'] == 0))) {
                    $isOk = false;
                }
                if (isset($duplicates[$entity['page_id']]) && $duplicates[$entity['page_id']][$attribute]) {
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
                    $duplicates[$attribute][$entity[$attribute]][] = $entity['page_id'];
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
                    if (!isset($duplicateProducts[$entity['page_id']])) {
                        $duplicateProducts[$entity['page_id']] = $_duplicateTmpl;
                        $duplicateProducts[$entity['page_id']]['page_id'] = $entity['page_id'];
                    }
                    $duplicateProducts[$entity['page_id']][$attribute] = serialize($duplicates[$attribute][$entity[$attribute]]);
                }
            }
        }
        return $duplicateProducts;
    }
}
