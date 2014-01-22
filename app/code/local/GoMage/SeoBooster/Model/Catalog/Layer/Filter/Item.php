<?php
/**
 * GoMage Seo Booster Extension
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
 * Layer filter item model
 *
 * @category   GoMage
 * @package    GoMage_Seobooster
 * @subpackage Model
 * @author     Roman Bublik <rb@gomage.com>
 */
class GoMage_SeoBooster_Model_Catalog_Layer_Filter_Item extends Mage_Catalog_Model_Layer_Filter_Item
{
    /**
     * Get filter item url
     *
     * @return string
     */
    public function getUrl()
    {
        $query = array(
            Mage::getBlockSingleton('page/html_pager')->getPageVarName() => null // exclude current page from urls
        );

        $params = array(
            '_current'=>true,
            '_use_rewrite'=>true,
        );

        if ($separator = Mage::helper('gomage_seobooster/layered')->getSeparator()) {
            $params['_layered_query_params'] = array($this->getFilter()->getRequestVar() => $this->getValue());
        } else {
            $query[$this->getFilter()->getRequestVar()] = $this->getValue();
        }
        $params['_query'] = $query;

        $routePath = '*/*/*';

        if ($rewritePath = Mage::helper('gomage_seobooster/layered')->getRewritePath()) {
            $routePath .= '/' . $rewritePath;
        }

        return Mage::helper('gomage_seobooster')->getUrl($routePath, $params);
    }

    /**
     * Get url for remove item from filter
     *
     * @return string
     */
    public function getRemoveUrl()
    {
        $params = array();
        $query = array();
        $layeredQuery = array();

        if ($separator = Mage::helper('gomage_seobooster/layered')->getSeparator()) {
            $layeredQuery = array($this->getFilter()->getRequestVar()=>$this->getFilter()->getResetValue());
        } else {
            $query = array($this->getFilter()->getRequestVar()=>$this->getFilter()->getResetValue());
        }

        $params['_current']     = true;
        $params['_use_rewrite'] = true;
        $params['_query']       = $query;
        $params['_escape']      = true;
        $params['_layered_query_params'] = $layeredQuery;

        $routePath = '*/*/*';

        if (($rewritePath = Mage::helper('gomage_seobooster/layered')->getRewritePath()) &&
            Mage::helper('gomage_seobooster/layered')->getFilterableParamsSize() > 1) {
            $routePath .= '/' . $rewritePath;
        }

        return Mage::helper('gomage_seobooster')->getUrl($routePath, $params);
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

        $query = array();
        $layeredQuery = array();

        if (Mage::helper('gomage_seobooster/layered')->getSeparator()) {
            $layeredQuery = array($this->getFilter()->getRequestVar() => null);
        } else {
            $query = array($this->getFilter()->getRequestVar() => null);
        }

        $urlParams = array(
            '_current' => true,
            '_use_rewrite' => true,
            '_query' => $query,
            '_escape' => true,
            '_layered_query_params' => $layeredQuery
        );
        $routePath = '*/*/*';

        if (($rewritePath = Mage::helper('gomage_seobooster/layered')->getRewritePath()) &&
            Mage::helper('gomage_seobooster/layered')->getFilterableParamsSize() > 1) {
            $routePath .= '/' . $rewritePath;
        }

        return Mage::helper('gomage_seobooster')->getUrl($routePath, $urlParams);
    }
}
