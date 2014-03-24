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

class GoMage_SeoBooster_Model_Analyzer extends Mage_Core_Model_Abstract
{
    const ANALYZER_PRODUCT = 'product';
    const ANALYZER_CATEGORY = 'category';
    const ANALYZER_PAGE = 'page';

    const REPORT_PRODUCT_ANALYZER_FLAG_CODE = 'report_product_analyzer';
    const REPORT_CATEGORY_ANALYZER_FLAG_CODE = 'report_category_analyzer';
    const REPORT_PAGE_ANALYZER_FLAG_CODE = 'report_page_analyzer';

    const LONG_ERROR = 1;
    const SHORT_ERROR = 2;
    const DUPLICATE_ERROR = 3;
    const MISSING_ERROR = 4;
    const RESULT_OK = 0;


    public function generateReport($type)
    {
        self::factory($type)->generateReport();
    }

    public static function factory($type)
    {
        switch ($type) {
            case self::ANALYZER_PRODUCT:
                return Mage::getModel('gomage_seobooster/analyzer_product');
            case self::ANALYZER_CATEGORY:
                return  Mage::getModel('gomage_seobooster/analyzer_category');
            case self::ANALYZER_PAGE:
                return  Mage::getModel('gomage_seobooster/analyzer_page');
        }
    }

    static public function getErrorsOptions()
    {
        return array(
            self::RESULT_OK  => Mage::helper('gomage_seobooster')->__('Ok'),
            self::LONG_ERROR => Mage::helper('gomage_seobooster')->__('Long'),
            self::SHORT_ERROR => Mage::helper('gomage_seobooster')->__('Short'),
            self::DUPLICATE_ERROR => Mage::helper('gomage_seobooster')->__('Duplicate'),
            self::MISSING_ERROR => Mage::helper('gomage_seobooster')->__('Missing'),
        );
    }
}
