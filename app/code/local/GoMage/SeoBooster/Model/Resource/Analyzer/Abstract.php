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

abstract class GoMage_SeoBooster_Model_Resource_Analyzer_Abstract extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Flag object
     *
     * @var Mage_Reports_Model_Flag
     */
    protected $_flag     = null;

    protected $_duplicatesTable;

    abstract public function getEntities();

    abstract public function prepareEntities($entities, $duplicates);

    abstract protected function _getDuplicateEntities($entities);

    abstract  public function generateReport();

    public function getDuplicateTable()
    {
        return $this->getTable($this->_duplicatesTable);
    }

    protected function _getMetaKeywordsQty($string)
    {
        $string = preg_replace('/\s+/', '', $string);
        if (empty($string)) {
            return 0;
        }

        $keywords = explode(',', $string);

        return count($keywords);
    }

    public function getDuplicateValues($entityId, $field)
    {
        $select = $this->_getWriteAdapter()->select()
            ->from(array('main_table' => $this->getDuplicateTable()), array($field))
            ->where("main_table.entity_id = ?", $entityId);

        $duplicates = $this->_getWriteAdapter()->fetchOne($select);
        $duplicates = unserialize($duplicates);

        return $duplicates;
    }

    /**
     * Retrive flag object
     *
     * @return Mage_Reports_Model_Flag
     */
    protected function _getFlag()
    {
        if ($this->_flag === null) {
            $this->_flag = Mage::getModel('reports/flag');
        }
        return $this->_flag;
    }

    /**
     * Saves flag
     *
     * @param string $code
     * @param mixed $value
     * @return Mage_Reports_Model_Resource_Report_Abstract
     */
    protected function _setFlagData($code, $value = null)
    {
        $this->_getFlag()
            ->setReportFlagCode($code)
            ->unsetData()
            ->loadSelf();

        if ($value !== null) {
            $this->_getFlag()->setFlagData($value);
        }

        $time = Varien_Date::toTimestamp(true);
        $this->_getFlag()->setLastUpdate($this->formatDate($time));

        $this->_getFlag()->save();

        return $this;
    }
}
