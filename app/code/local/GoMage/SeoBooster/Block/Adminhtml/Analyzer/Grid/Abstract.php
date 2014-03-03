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
 * @subpackage Block
 * @author     Roman Bublik <rb@gomage.com>
 */
abstract class GoMage_SeoBooster_Block_Adminhtml_Analyzer_Grid_Abstract extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Init the grid
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('things_grid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(false);
        $this->setUseAjax(false);
    }

    /**
     * Prepare grid collection
     *
     * @return Oggetto_Things_Block_Adminhtml_Thing_Grid
     */
    protected function _prepareCollection()
    {
        return parent::_prepareCollection();
    }

    /**
     * Define grid columns
     *
     * @return Oggetto_Things_Block_Adminhtml_Thing_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header' => $this->helper('gomage_seobooster')->__('Name'),
            'index'  => 'name',
            'options' => GoMage_SeoBooster_Model_Analyzer::getErrorsOptions(),
            'type'    => 'options',
            'renderer' => 'gomage_seobooster/adminhtml_analyzer_grid_renderer_options'
        ));

        $this->addColumn('description', array(
            'header' => $this->helper('gomage_seobooster')->__('Description'),
            'index'  => 'description',
            'type'   => 'options',
            'options' => GoMage_SeoBooster_Model_Analyzer::getErrorsOptions(),
            'renderer' => 'gomage_seobooster/adminhtml_analyzer_grid_renderer_options'
        ));

        $this->addColumn('meta_title', array(
            'header' => $this->helper('gomage_seobooster')->__('Title'),
            'index'  => 'meta_title',
            'type'    => 'options',
            'renderer' => 'gomage_seobooster/adminhtml_analyzer_grid_renderer_options',
            'options' => GoMage_SeoBooster_Model_Analyzer::getErrorsOptions(),
        ));

        $this->addColumn('meta_description', array(
            'header' => $this->helper('gomage_seobooster')->__('Meta Description'),
            'index'  => 'meta_description',
            'type'    => 'options',
            'renderer' => 'gomage_seobooster/adminhtml_analyzer_grid_renderer_options',
            'options' => GoMage_SeoBooster_Model_Analyzer::getErrorsOptions()
        ));

        $this->addColumn('meta_keyword', array(
            'header' => $this->helper('gomage_seobooster')->__('Meta Keywords'),
            'index'  => 'meta_keyword',
            'type'    => 'options',
            'renderer' => 'gomage_seobooster/adminhtml_analyzer_grid_renderer_options',
            'options' => GoMage_SeoBooster_Model_Analyzer::getErrorsOptions()
        ));

        return parent::_prepareColumns();
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            $field = ( $column->getFilterIndex() ) ? $column->getFilterIndex() : $column->getIndex();
            if ($column->getFilterConditionCallback()) {
                call_user_func($column->getFilterConditionCallback(), $this->getCollection(), $column);
            } else {
                $fieldsMap = GoMage_SeoBooster_Model_Resource_Analayzer_Product_Collection::getFieldsMap();
                if (isset($fieldsMap[$field])) {
                    switch ($column->getFilter()->getValue()) {
                        case GoMage_SeoBooster_Model_Analyzer::LONG_ERROR:
                            $condition = array('gt' => Mage::helper('gomage_seobooster/analyzer')->getCharsCountLimit($field));
                            $field = $fieldsMap[$field];
                            Zend_Debug::dump($field);
                            break;
                        case GoMage_SeoBooster_Model_Analyzer::SHORT_ERROR:
                            $condition = array('lt' => Mage::helper('gomage_seobooster/analyzer')->getMinCharsCountLimit($field));
                            $field = $fieldsMap[$field];
                            break;
                        case GoMage_SeoBooster_Model_Analyzer::MISSING:
                            $condition = array(
                                'to' => Mage::helper('gomage_seobooster/analyzer')->getCharsCountLimit($field),
                                'from' => Mage::helper('gomage_seobooster/analyzer')->getMinCharsCountLimit($field)
                            );
                            $duplicateField = 'duplicate_table.'.$field;
                            $field = $fieldsMap[$field];
                            $this->getCollection()->addFieldToFilter($field , $condition);
//                            $this->getCollection()->addFieldToFilter($duplicateField , array('null' => true));
                            $this->getCollection()->addFieldToFilter($duplicateField , array('eq' =>''));
                            return $this;
                        case GoMage_SeoBooster_Model_Analyzer::DUPLICATE_ERROR:
                            $field = 'duplicate_table.'.$field;
                            $this->getCollection()->addFieldToFilter($field , array('notnull' => true));
                            $this->getCollection()->addFieldToFilter($field , array('neq' =>''));
                            return $this;
                    }
                } else {
                    $condition = $column->getFilter()->getCondition();
                }

                if ($field && isset($condition)) {
                    $this->getCollection()->addFieldToFilter($field , $condition);
                }
            }
        }
        return $this;
    }
}
