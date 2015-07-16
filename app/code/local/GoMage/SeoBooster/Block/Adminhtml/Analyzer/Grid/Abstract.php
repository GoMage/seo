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
abstract class GoMage_SeoBooster_Block_Adminhtml_Analyzer_Grid_Abstract extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Init the grid
     */
    public function __construct()
    {
        parent::__construct();
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(false);
        $this->setUseAjax(false);
    }

    /**
     * Define grid columns
     *
     * @return Oggetto_Things_Block_Adminhtml_Thing_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn(GoMage_SeoBooster_Helper_Analyzer::NAME_FIELD, array(
                'header'           => $this->helper('gomage_seobooster')->__('Name'),
                'index'            => GoMage_SeoBooster_Helper_Analyzer::NAME_FIELD,
                'options'          => GoMage_SeoBooster_Model_Analyzer::getErrorsOptions(),
                'type'             => 'options',
                'renderer'         => 'gomage_seobooster/adminhtml_analyzer_grid_renderer_options',
                'duplicate_action' => $this->_getDuplicateAction()
            )
        );

        $this->addColumn(GoMage_SeoBooster_Helper_Analyzer::DESCRIPTION_FIELD, array(
                'header'           => $this->helper('gomage_seobooster')->__('Description'),
                'index'            => GoMage_SeoBooster_Helper_Analyzer::DESCRIPTION_FIELD,
                'type'             => 'options',
                'options'          => GoMage_SeoBooster_Model_Analyzer::getErrorsOptions(),
                'renderer'         => 'gomage_seobooster/adminhtml_analyzer_grid_renderer_options',
                'duplicate_action' => $this->_getDuplicateAction()
            )
        );

        $this->addColumn(GoMage_SeoBooster_Helper_Analyzer::META_TITLE_FIELD, array(
                'header'           => $this->helper('gomage_seobooster')->__('Title'),
                'index'            => GoMage_SeoBooster_Helper_Analyzer::META_TITLE_FIELD,
                'type'             => 'options',
                'renderer'         => 'gomage_seobooster/adminhtml_analyzer_grid_renderer_options',
                'options'          => GoMage_SeoBooster_Model_Analyzer::getErrorsOptions(),
                'duplicate_action' => $this->_getDuplicateAction()
            )
        );

        $this->addColumn(GoMage_SeoBooster_Helper_Analyzer::META_DESCRIPTION_FIELD, array(
                'header'           => $this->helper('gomage_seobooster')->__('Meta Description'),
                'index'            => GoMage_SeoBooster_Helper_Analyzer::META_DESCRIPTION_FIELD,
                'type'             => 'options',
                'renderer'         => 'gomage_seobooster/adminhtml_analyzer_grid_renderer_options',
                'options'          => GoMage_SeoBooster_Model_Analyzer::getErrorsOptions(),
                'duplicate_action' => $this->_getDuplicateAction()
            )
        );

        $this->addColumn(GoMage_SeoBooster_Helper_Analyzer::META_KEYWORD_FIELD, array(
                'header'           => $this->helper('gomage_seobooster')->__('Meta Keywords'),
                'index'            => GoMage_SeoBooster_Helper_Analyzer::META_KEYWORD_FIELD,
                'type'             => 'options',
                'renderer'         => 'gomage_seobooster/adminhtml_analyzer_grid_renderer_options',
                'options'          => GoMage_SeoBooster_Model_Analyzer::getErrorsOptions(),
                'duplicate_action' => $this->_getDuplicateAction()
            )
        );

        return parent::_prepareColumns();
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            $field = ($column->getFilterIndex()) ? $column->getFilterIndex() : $column->getIndex();
            if ($column->getFilterConditionCallback()) {
                call_user_func($column->getFilterConditionCallback(), $this->getCollection(), $column);
            } else {
                $fieldsMap = GoMage_SeoBooster_Model_Resource_Analyzer_Collection_Abstract::getFieldsMap();
                if (isset($fieldsMap[$field])) {
                    switch ($column->getFilter()->getValue()) {
                        case GoMage_SeoBooster_Model_Analyzer::LONG_ERROR:
                            $condition = array('gt' => Mage::helper('gomage_seobooster/analyzer')->getCharsCountLimit($field));
                            $field     = $fieldsMap[$field];
                            break;
                        case GoMage_SeoBooster_Model_Analyzer::SHORT_ERROR:
                            $minLimit  = Mage::helper('gomage_seobooster/analyzer')->getMinCharsCountLimit($field);
                            $condition = array('lt' => $minLimit);
                            $field     = $fieldsMap[$field];
                            break;
                        case GoMage_SeoBooster_Model_Analyzer::MISSING_ERROR:
                            $condition = array('eq' => 0);
                            $field     = $fieldsMap[$field];
                            break;
                        case GoMage_SeoBooster_Model_Analyzer::DUPLICATE_ERROR:
                            $field = 'duplicate_' . $field;
                            $this->getCollection()->addFieldToFilter($field, array('notnull' => true));
                            return $this;
                        case GoMage_SeoBooster_Model_Analyzer::RESULT_OK:
                            $minLimit = Mage::helper('gomage_seobooster/analyzer')->getMinCharsCountLimit($field);
                            $maxLimit = Mage::helper('gomage_seobooster/analyzer')->getCharsCountLimit($field);
                            $this->getCollection()->addFieldToFilter($fieldsMap[$field], array('from' => $minLimit, 'to' => $maxLimit));
                            $this->getCollection()->addFieldToFilter($fieldsMap[$field], array('neq' => 0));
                            $this->getCollection()->addFieldToFilter('duplicate_' . $field, array('null' => true));
                            return $this;
                    }
                } else {
                    $condition = $column->getFilter()->getCondition();
                }

                if ($field && isset($condition)) {
                    $this->getCollection()->addFieldToFilter($field, $condition);
                }
            }
        }
        return $this;
    }

    abstract protected function _getDuplicateAction();
}
