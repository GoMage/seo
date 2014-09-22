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
 * @since        Available since Release 1.1.0
 */
class GoMage_SeoBoosterBridge_Block_Layer_Searchview extends GoMage_Navigation_Block_Layer_Searchview
{

    public function getClearUrl($ajax = false)
    {
        $filterState = array();

        foreach ($this->getActiveFilters() as $item) {
            try {
                switch ($item->getFilter()->getAttributeModel()->getFilterType()) {
                    case (GoMage_Navigation_Model_Layer::FILTER_TYPE_INPUT):
                        $filterState[$item->getFilter()->getRequestVarValue() . '_from'] = null;
                        $filterState[$item->getFilter()->getRequestVarValue() . '_to']   = null;
                        break;
                    case (GoMage_Navigation_Model_Layer::FILTER_TYPE_SLIDER):
                    case (GoMage_Navigation_Model_Layer::FILTER_TYPE_SLIDER_INPUT):
                    case (GoMage_Navigation_Model_Layer::FILTER_TYPE_INPUT_SLIDER):
                        if (Mage::helper('gomage_navigation')->isMobileDevice()) {
                            $filterState[$item->getFilter()->getRequestVarValue()] = $item->getFilter()->getResetValue();
                        } else {
                            $filterState[$item->getFilter()->getRequestVarValue() . '_from'] = null;
                            $filterState[$item->getFilter()->getRequestVarValue() . '_to']   = null;
                        }
                        break;
                    case (GoMage_Navigation_Model_Layer::FILTER_TYPE_DEFAULT):
                        if ($item->getFilter()->getAttributeModel()->getRangeOptions() != GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Optionsrange::NO) {
                            $filterState[$item->getFilter()->getRequestVarValue() . '_from'] = null;
                            $filterState[$item->getFilter()->getRequestVarValue() . '_to']   = null;
                        } else {
                            $filterState[$item->getFilter()->getRequestVarValue()] = $item->getFilter()->getResetValue();
                        }
                        break;
                    default:
                        $filterState[$item->getFilter()->getRequestVarValue()] = $item->getFilter()->getResetValue();
                        break;
                }
            } catch (Exception $e) {
                $filterState[$item->getFilter()->getRequestVarValue()] = $item->getFilter()->getResetValue();
            }
        }

        $params['_layered_query_params'] = $filterState;

        $params['_nosid']       = true;
        $params['_current']     = true;
        $params['_use_rewrite'] = true;

        $params['_escape'] = true;

        $params['_query']['ajax'] = null;

        if ($ajax) {
            $params['_query']['ajax'] = true;
        }

        return Mage::helper('gomage_seobooster/layered')->getUrl('*/*/*', $params);
    }

}
