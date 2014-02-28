<?php
/**
 * GoMage SeoBooster Extension
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
 * SeoBooster seo analyzer report controller
 *
 * @category   GoMage
 * @package    GoMage_SeoBooster
 * @subpackage controllers
 * @author     Roman Bublik <rb@gomage.com>
 */
class GoMage_SeoBooster_Adminhtml_Report_Seo_AnalyzerController extends Mage_Adminhtml_Controller_Action
{
    public function productsAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function analyzeAction()
    {
        $type = $this->getRequest()->getParam('type');
        if ($type) {
            Mage::getModel('gomage_seobooster/analyzer')->generateReport($type);
        }

        $this->_redirect('*/*/products');
    }
}