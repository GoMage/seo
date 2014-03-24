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

class GoMage_SeoBooster_Block_Adminhtml_Analyzer_Duplicate_Grid_Abstract extends Mage_Adminhtml_Block_Widget_Grid
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

    protected function _prepareColumns()
    {
        $analyzeFields = Mage::helper('gomage_seobooster/analyzer')->getAnalyzeFields();
        $field = $this->_getDuplicateField();
        if (in_array($field, $analyzeFields) && $field != GoMage_SeoBooster_Helper_Analyzer::NAME_FIELD) {
            $this->addColumn($field, array(
                'header' => Mage::helper('gomage_seobooster/analyzer')->getAnalyzeFieldLabel($field),
                'index'  => $field,
                'type'    => 'text'
            ));
        }

        return parent::_prepareColumns();
    }

    protected function _getDuplicateField()
    {
        return $this->getRequest()->getParam('duplicate_field');
    }
}
