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
class GoMage_SeoBoosterBridge_Block_Catalog_Layer_Filter_Price extends GoMage_Navigation_Block_Layer_Filter_Price
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

    protected function _prepareFilter()
    {
        parent::_prepareFilter();

        if (Mage::helper('gomage_navigation')->isGomageNavigation()) {

            switch ($this->getAttributeModel()->getFilterType()) {
                case(GoMage_Navigation_Model_Layer::FILTER_TYPE_INPUT):
                    $this->_template = ('gomage/navigation/layer/filter/input.phtml');
                    break;
                case(GoMage_Navigation_Model_Layer::FILTER_TYPE_SLIDER):
                    if (Mage::helper('gomage_navigation')->isMobileDevice()) {
                        $this->_template = ('gomage/seoboosterbridge/layer/filter/default.phtml');
                    } else {
                        $this->_template = ('gomage/navigation/layer/filter/slider.phtml');
                    }
                    break;
                case(GoMage_Navigation_Model_Layer::FILTER_TYPE_SLIDER_INPUT):
                    if (Mage::helper('gomage_navigation')->isMobileDevice()) {
                        $this->_template = ('gomage/seoboosterbridge/layer/filter/default.phtml');
                    } else {
                        $this->_template = ('gomage/navigation/layer/filter/slider-input.phtml');
                    }
                    break;
                case(GoMage_Navigation_Model_Layer::FILTER_TYPE_INPUT_SLIDER):
                    if (Mage::helper('gomage_navigation')->isMobileDevice()) {
                        $this->_template = ('gomage/seoboosterbridge/layer/filter/default.phtml');
                    } else {
                        $this->_template = ('gomage/navigation/layer/filter/input-slider.phtml');
                    }
                    break;
                case(GoMage_Navigation_Model_Layer::FILTER_TYPE_DROPDOWN):
                    $this->_template = ('gomage/seoboosterbridge/layer/filter/dropdown.phtml');
                    break;
                default:
                    $this->_template = ('gomage/seoboosterbridge/layer/filter/default.phtml');
                    break;
            }
        }
    }

}
