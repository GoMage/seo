<?php

/**
 * GoMage Seo Booster Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2013-2014 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use/
 * @version      Release: 1.1.0
 * @since        Available since Release 1.0.0
 */
class GoMage_SeoBoosterBridge_Block_Catalog_Layer_Filter_Category extends GoMage_Navigation_Block_Layer_Filter_Category
{
    /**
     * Init filter model object
     *
     * @return Mage_Catalog_Block_Layer_Filter_Abstract
     */
    protected function _initFilter()
    {
        if (!$this->_filterModelName) {
            Mage::throwException(Mage::helper('catalog')->__('Filter model name must be declared.'));
        }
        $this->_filter = Mage::getModel($this->_filterModelName)
            ->setLayer($this->getLayer());
        $this->_prepareFilter();
        $request = $this->getRequest();
        $helper  = Mage::helper('gomage_seobooster/layered');
        $request = $helper->getSeparator() || $helper->canAddRewritePath() ? $helper->getRequest() : $request;

        $this->_filter->apply($request, $this);
        return $this;
    }

    public function setCustomTemplate()
    {
        $type = Mage::getStoreConfig('gomage_navigation/' . $this->getConfigTab() . '/filter_type');

        switch ($type) {
            default:
                $this->_template = ('gomage/seoboosterbridge/layer/filter/category/default.phtml');
                break;
            case(GoMage_Navigation_Model_Layer::FILTER_TYPE_IMAGE):
                $this->_template = ('gomage/seoboosterbridge/layer/filter/image.phtml');
                break;
            case(GoMage_Navigation_Model_Layer::FILTER_TYPE_DROPDOWN):
                $this->_template = ('gomage/seoboosterbridge/layer/filter/dropdown.phtml');
                break;
        }
    }

    public function __construct()
    {
        parent::__construct();

        if (Mage::helper('gomage_navigation')->isGomageNavigation()
            &&
            (Mage::getStoreConfigFlag('gomage_navigation/category/active')
                ||
                Mage::getStoreConfigFlag('gomage_navigation/rightcolumnsettings/active')
                ||
                Mage::getStoreConfigFlag('gomage_navigation/contentcolumnsettings/active')
            )
        ) {

            $type = Mage::getStoreConfig('gomage_navigation/category/filter_type');

            switch ($type) {
                default:
                    $this->_template = ('gomage/seoboosterbridge/layer/filter/category/default.phtml');
                    break;
                case(GoMage_Navigation_Model_Layer::FILTER_TYPE_IMAGE):
                    $this->_template = ('gomage/seoboosterbridge/layer/filter/image.phtml');
                    break;
                case(GoMage_Navigation_Model_Layer::FILTER_TYPE_DROPDOWN):
                    $this->_template = ('gomage/seoboosterbridge/layer/filter/dropdown.phtml');
                    break;
            }

        }

    }

}
