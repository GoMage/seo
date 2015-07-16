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

abstract class GoMage_SeoBooster_Model_Resource_Analyzer_Collection_Abstract
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected $_addErrors = false;

    abstract public function prepareCollectionForReport();

    abstract public function getDuplicateCollection($entityId);

    public static function getFieldsMap()
    {
        return array(
            GoMage_SeoBooster_Helper_Analyzer::NAME_FIELD => 'name_chars_count',
            GoMage_SeoBooster_Helper_Analyzer::DESCRIPTION_FIELD => 'description_chars_count',
            GoMage_SeoBooster_Helper_Analyzer::META_TITLE_FIELD => 'meta_title_chars_count',
            GoMage_SeoBooster_Helper_Analyzer::META_DESCRIPTION_FIELD => 'meta_description_chars_count',
            GoMage_SeoBooster_Helper_Analyzer::META_KEYWORD_FIELD => 'meta_keyword_chars_count'
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

    public function addErrors($value)
    {
        $this->_addErrors = $value;
        return $this;
    }

    protected function _getDuplicateField()
    {
        return Mage::app()->getRequest()->getParam('duplicate_field');
    }
}
