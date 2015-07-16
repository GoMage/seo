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

class GoMage_SeoBooster_Block_Adminhtml_Analyzer_Page
    extends GoMage_SeoBooster_Block_Adminhtml_Analyzer_Abstract
{
    /**
     * Init grid container
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_analyzer_page';
        $this->_blockGroup = 'gomage_seobooster';
        $this->_headerText = $this->helper('gomage_seobooster')->__('Pages Analyzer');
        parent::__construct();
    }

    protected function _getAnalyzerUrl()
    {
        return Mage::helper('adminhtml')->getUrl('*/*/analyze', array(
            'type' => GoMage_SeoBooster_Model_Analyzer::ANALYZER_PAGE
        ));
    }
}
