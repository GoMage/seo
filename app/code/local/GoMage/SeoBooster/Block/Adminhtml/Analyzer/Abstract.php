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

abstract class GoMage_SeoBooster_Block_Adminhtml_Analyzer_Abstract extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Init grid container
     */
    public function __construct()
    {
        parent::__construct();
        $this->_addButton('analyze', array(
            'label'     => $this->helper('gomage_seobooster')->__('Analyze'),
            'onclick'   => 'setLocation(\''. $this->_getAnalyzerUrl() .'\')',
            'class'     => 'save',
        ));
        $this->removeButton('add');
    }

    abstract protected function _getAnalyzerUrl();

    /**
     * Check whether it is single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        if (!Mage::app()->isSingleStoreMode()) {
            return false;
        }
        return true;
    }
}
