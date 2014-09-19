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
class GoMage_SeoBoosterBridge_Model_Catalog_Layer_Filter_Item extends GoMage_Navigation_Model_Layer_Filter_Item
{

    /**
     * @param bool $ajax
     * @param bool $stock
     * @return mixed
     */
    public function getUrl($ajax = false, $stock = false)
    {
        $query = array(
            Mage::getBlockSingleton('page/html_pager')->getPageVarName() => null // exclude current page from urls
        );

        $params = array(
            '_current'     => true,
            '_use_rewrite' => true,
        );

        if ($separator = Mage::helper('gomage_seobooster/layered')->getSeparator() || Mage::helper('gomage_seobooster/layered')->canAddRewritePath()) {
            $params['_layered_query_params'] = array($this->getFilter()->getRequestVar() => $this->getValue());
        } else {
            $query[$this->getFilter()->getRequestVar()] = $this->getValue();
        }

        $query['ajax'] = null;
        if ($ajax) {
            $query['ajax'] = 1;
        }

        $params['_query'] = $query;

        return Mage::helper('gomage_seobooster/layered')->getUrl('*/*/*', $params);
    }

    /**
     * @param bool $ajax
     * @return mixed
     */
    public function getRemoveUrl($ajax = false)
    {

        $query = array(
            Mage::getBlockSingleton('page/html_pager')->getPageVarName() => null // exclude current page from urls
        );

        $params = array(
            '_current'     => true,
            '_use_rewrite' => true,
            '_escape'      => true,
        );

        $reset_value = $this->getFilter()->getResetValue($this->getValue());

        if ($separator = Mage::helper('gomage_seobooster/layered')->getSeparator() || Mage::helper('gomage_seobooster/layered')->canAddRewritePath()) {
            $params['_layered_query_params'] = array($this->getFilter()->getRequestVar() => $reset_value);
            if (in_array($this->getFilter()->getAttributeModel()->getFilterType(), array(
                    GoMage_Navigation_Model_Layer::FILTER_TYPE_SLIDER,
                    GoMage_Navigation_Model_Layer::FILTER_TYPE_INPUT,
                    GoMage_Navigation_Model_Layer::FILTER_TYPE_INPUT_SLIDER,
                    GoMage_Navigation_Model_Layer::FILTER_TYPE_SLIDER_INPUT,)
            )
            ) {
                $params['_layered_query_params'][$this->getFilter()->getRequestVar() . '_from'] = null;
                $params['_layered_query_params'][$this->getFilter()->getRequestVar() . '_to']   = null;
            }
        } else {
            $query[$this->getFilter()->getRequestVar()] = $reset_value;
            if (in_array($this->getFilter()->getAttributeModel()->getFilterType(), array(
                    GoMage_Navigation_Model_Layer::FILTER_TYPE_SLIDER,
                    GoMage_Navigation_Model_Layer::FILTER_TYPE_INPUT,
                    GoMage_Navigation_Model_Layer::FILTER_TYPE_INPUT_SLIDER,
                    GoMage_Navigation_Model_Layer::FILTER_TYPE_SLIDER_INPUT,)
            )
            ) {
                $query[$this->getFilter()->getRequestVar() . '_from'] = null;
                $query[$this->getFilter()->getRequestVar() . '_to']   = null;
            }
        }

        if (Mage::helper('gomage_seobooster/layered')->getFilterableParamsSize() < 2) {
            $params['_use_layer_rewrite'] = false;
        }

        $query['ajax'] = null;
        if ($ajax) {
            $query['ajax'] = 1;
        }

        $params['_query'] = $query;

        return Mage::helper('gomage_seobooster/layered')->getUrl('*/*/*', $params);

    }

    /**
     * Get url for "clear" link
     *
     * @return false|string
     */
    public function getClearLinkUrl()
    {
        $clearLinkText = $this->getFilter()->getClearLinkText();
        if (!$clearLinkText) {
            return false;
        }

        $query        = array();
        $layeredQuery = array();

        if (Mage::helper('gomage_seobooster/layered')->getSeparator() ||
            Mage::helper('gomage_seobooster/layered')->canAddRewritePath()
        ) {
            $layeredQuery = array($this->getFilter()->getRequestVar() => null);
        } else {
            $query = array($this->getFilter()->getRequestVar() => null);
        }

        if (Mage::helper('gomage_seobooster/layered')->getFilterableParamsSize() < 2) {
            $params['_use_layer_rewrite'] = false;
        }

        $urlParams = array(
            '_current'              => true,
            '_use_rewrite'          => true,
            '_query'                => $query,
            '_escape'               => true,
            '_layered_query_params' => $layeredQuery
        );

        return Mage::helper('gomage_seobooster/layered')->getUrl('*/*/*', $urlParams);
    }

}
