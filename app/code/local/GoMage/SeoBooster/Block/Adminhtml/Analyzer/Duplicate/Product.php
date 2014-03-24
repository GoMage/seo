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

class GoMage_SeoBooster_Block_Adminhtml_Analyzer_Duplicate_Product
    extends GoMage_SeoBooster_Block_Adminhtml_Analyzer_Duplicate_Abstract
{
    /**
     * Init grid container
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_analyzer_duplicate_product';
        $this->_blockGroup = 'gomage_seobooster';
        $this->_headerText = $this->helper('gomage_seobooster')->__('Products Analyzer - View Duplicates');
        parent::__construct();
    }

    protected function _prepareLayout()
    {
        $this->setChild( 'grid',
            $this->getLayout()->createBlock( $this->_blockGroup.'/adminhtml_analyzer_product_duplicate_grid',
                $this->_controller . '.grid')->setSaveParametersInSession(true));
        return Mage_Adminhtml_Block_Widget_Container::_prepareLayout();
    }
}
