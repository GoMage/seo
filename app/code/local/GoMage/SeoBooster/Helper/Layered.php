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
 * Layered navigation helper
 *
 * @category   GoMage
 * @package    GoMage_SeoBooster
 * @subpackage Helper
 * @author     Roman Bublik <rb@gomage.com>
 */
class GoMage_SeoBooster_Helper_Layered extends Mage_Core_Helper_Data
{
    protected $_request;

    protected $_filterableParamsCount = null;

    /**
     * Is url rewrite for layered navigation enabled
     *
     * @return bool
     */
    public function isUrlRewriteEnabled()
    {
        return Mage::helper('gomage_seobooster')->isEnabled()
            && Mage::getStoreConfig('gomage_seobooster/url_rewrite/enable_layered_url_rewrite');
    }

    /**
     * Can use friendly urls for layered navigation
     *
     * @return bool
     */
    public function canUseFriendlyUrl()
    {
        return $this->isUrlRewriteEnabled()
            && Mage::getStoreConfig('gomage_seobooster/url_rewrite/layered_friendly_urls');
    }

    /**
     * Return separator for layered navigation params
     *
     * @return string|bool
     */
    public function getSeparator()
    {
        return $this->isUrlRewriteEnabled()
            && Mage::getStoreConfig('gomage_seobooster/url_rewrite/layered_separator') != '='
            ? Mage::getStoreConfig('gomage_seobooster/url_rewrite/layered_separator') : false;
    }

    /**
     * Return can add rewrite path to layered url
     *
     * @return bool
     */
    public function canAddRewritePath()
    {
        return $this->isUrlRewriteEnabled()
            && Mage::getStoreConfig('gomage_seobooster/url_rewrite/enable_layered_rewrite_path')
            && Mage::getStoreConfig('gomage_seobooster/url_rewrite/layered_rewrite_path');
    }

    /**
     * Return rewrite path for layered url
     *
     * @return string
     */
    public function getRewritePath()
    {
        return $this->canAddRewritePath() ? Mage::getStoreConfig('gomage_seobooster/url_rewrite/layered_rewrite_path')
            : false;
    }

    /**
     * Return query string for layered
     *
     * @param array $params  Query params
     * @param bool  $escape Escape string
     * @return string
     */
    public function getQuery($params, $escape)
    {
        $query = '';

        if (is_array($params)) {
            $paramsSeparator = $escape ? '&amp;' : '&';
            $queryParams = array();
            ksort($params);
            if ($valueSeparator = $this->getSeparator()) {
                foreach ($params as $_param => $_value) {
                    if (!is_null($_value)) {
                        $queryParams[] = $_param . $valueSeparator . $_value;
                    }
                }
                $query = implode($paramsSeparator, $queryParams);
            } else {
                $query = http_build_query($params, '', $paramsSeparator);
            }
        }

        return $query;
    }

    /**
     * Check is query param from layer
     *
     * @param string $param Param
     * @param string $value Value
     * @return bool
     */
    public function isLayeredQueryParam($param, $value)
    {
        if (!$value) {
            $params = $this->prepareLayeredQueryParam($param);
            return count($params) < 2 ? false : true;
        }

        return false;
    }

    /**
     * Prepare layer param for query
     *
     * @param string $param Param
     * @return array
     */
    public function prepareLayeredQueryParam($param)
    {
        if ($separator = $this->getSeparator()) {
            $param = explode($separator, $param);
            if (count($param) > 2) {
                $key = $param[0];
                unset($param[0]);
                return array($key, implode($separator, $param));
            }
            return $param;
        }

        return array();
    }

    /**
     * Return new request object with prepared layer params
     *
     * @return Mage_Core_Controller_Request_Http
     */
    public function getRequest()
    {
        if (is_null($this->_request)) {
            $request = new Mage_Core_Controller_Request_Http();
            $params = $request->getParams();
            $request->clearParams();
            foreach ($params as $key => $value) {
                if ($this->isLayeredQueryParam($key, $value)) {
                    $param = $this->prepareLayeredQueryParam($key);
                    if (count($param) == 2) {
                        $request->setParam($param[0], $param[1]);
                    }
                }
            }
            $this->_request = $request;
        }

        return $this->_request;
    }

    /**
     * Return filterable params count
     *
     * @return int
     */
    public function getFilterableParamsSize()
    {
        if (is_null($this->_filterableParamsCount)) {
            $params = $this->getRequest()->getParams();
            $attributes = array();
            foreach ($params as $key => $value) {
                if ($value) {
                    $attributes[] = $key;
                }
            }
            $collection = Mage::getResourceModel('catalog/product_attribute_collection')->addIsFilterableFilter();
            $collection->addFieldToFilter('main_table.attribute_code', array('in' => $attributes));

            $this->_filterableParamsCount = $collection->getSize();
        }

        return $this->_filterableParamsCount;
    }
}
