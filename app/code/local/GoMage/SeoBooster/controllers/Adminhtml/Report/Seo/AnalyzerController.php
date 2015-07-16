<?php

/**
 * GoMage SeoBooster Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2013-2015 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use/
 * @version      Release: 1.3.0
 * @since        Available since Release 1.0.0
 */
class GoMage_SeoBooster_Adminhtml_Report_Seo_AnalyzerController extends Mage_Adminhtml_Controller_Action
{

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('report/gomage_seobooster/products_analyzer') ||
        Mage::getSingleton('admin/session')->isAllowed('report/gomage_seobooster/categories_analyzer') ||
        Mage::getSingleton('admin/session')->isAllowed('report/gomage_seobooster/pages_analyzer');
    }

    public function productAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('SEO Booster'))->_title($this->__('Products Analyzer'));
        $this->_showLastAnalyzeTime(GoMage_SeoBooster_Model_Analyzer::REPORT_PRODUCT_ANALYZER_FLAG_CODE);
        $this->loadLayout();
        $this->_setActiveMenu('report/gomage_seobooster/products_analyzer');
        $this->renderLayout();
    }

    public function productduplicateAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('SEO Booster'))->_title($this->__('Products Analyzer - View Duplicates'));
        $this->loadLayout();
        $this->_setActiveMenu('report/gomage_seobooster/products_analyzer');
        $this->renderLayout();
    }

    public function categoryAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('SEO Booster'))->_title($this->__('Categories Analyzer'));
        $this->_showLastAnalyzeTime(GoMage_SeoBooster_Model_Analyzer::REPORT_CATEGORY_ANALYZER_FLAG_CODE);
        $this->loadLayout();
        $this->_setActiveMenu('report/gomage_seobooster/categories_analyzer');
        $this->renderLayout();
    }

    public function categoryduplicateAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('SEO Booster'))->_title($this->__('Categories Analyzer - View Duplicates'));
        $this->loadLayout();
        $this->_setActiveMenu('report/gomage_seobooster/categories_analyzer');
        $this->renderLayout();
    }

    public function pageAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('SEO Booster'))->_title($this->__('Pages Analyzer'));
        $this->_showLastAnalyzeTime(GoMage_SeoBooster_Model_Analyzer::REPORT_PAGE_ANALYZER_FLAG_CODE);
        $this->loadLayout();
        $this->_setActiveMenu('report/gomage_seobooster/pages_analyzer');
        $this->renderLayout();
    }

    public function pageduplicateAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('SEO Booster'))->_title($this->__('Pages Analyzer - View Duplicates'));
        $this->loadLayout();
        $this->_setActiveMenu('report/gomage_seobooster/pages_analyzer');
        $this->renderLayout();
    }

    public function analyzeAction()
    {
        $type = $this->getRequest()->getParam('type');
        if (Mage::helper('gomage_seobooster')->isEnabled()) {
            if ($type) {
                Mage::getModel('gomage_seobooster/analyzer')->generateReport($type);
            }
        }

        $this->_redirect('*/*/' . $type);
    }

    protected function _showLastAnalyzeTime($flagCode)
    {
        $flag      = Mage::getModel('reports/flag')->setReportFlagCode($flagCode)->loadSelf();
        $updatedAt = ($flag->hasData())
            ? Mage::app()->getLocale()->storeDate(
                0, new Zend_Date($flag->getLastUpdate(), Varien_Date::DATETIME_INTERNAL_FORMAT), true
            )
            : 'undefined';

        Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('adminhtml')->__('Last updated: %s. To refresh last day\'s statistics, click Analyze.', $updatedAt));
        return $this;
    }
}