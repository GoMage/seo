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
class GoMage_SeoBoosterBridge_Helper_Data extends Mage_Core_Helper_Abstract
{

    protected $_slider_request_params = null;

    public function getSliderRequestParams()
    {
        if (is_null($this->_slider_request_params)) {

            $this->_slider_request_params = array();

            $collection = Mage::getResourceModel('catalog/product_attribute_collection')->addIsFilterableFilter();

            foreach ($collection as $attribute) {
                $an_attribute = Mage::getModel('gomage_navigation/attribute')->load($attribute->getAttributeId());
                if ($an_attribute && in_array($an_attribute->getFilterType(), array(
                            GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_SLIDER,
                            GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_INPUT,
                            GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_INPUT_SLIDER,
                            GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_SLIDER_INPUT,)
                    )
                ) {
                    $this->_slider_request_params[] = $attribute->getAttributeCode() . '_from';
                    $this->_slider_request_params[] = $attribute->getAttributeCode() . '_to';
                }
            }
        }

        return $this->_slider_request_params;
    }

    public function getCurrentUrl($query = array(), $ajax = false)
    {
        if (!$ajax) {
            $query['ajax'] = null;
        } else {
            $query['ajax'] = $ajax;
        }

        $layered_query_params = array();

        foreach ($query as $param => $value) {
            if (Mage::helper('gomage_seobooster/layered')->isLayeredQueryParam($param, $value)) {
                $layered_query_params[$param] = $value;
                unset($query[$param]);
            }
        }

        $params = array(
            '_current'     => true,
            '_use_rewrite' => true,
            '_escape'      => true,
            '_query'       => $query,
        );

        if (!empty($layered_query_params)) {
            $params['_layered_query_params'] = $layered_query_params;
        }

        return Mage::helper('gomage_seobooster/layered')->getUrl('*/*/*', $params);
    }

}
	 