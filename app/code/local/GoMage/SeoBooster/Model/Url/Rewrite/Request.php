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
class GoMage_SeoBooster_Model_Url_Rewrite_Request extends Mage_Core_Model_Url_Rewrite_Request
{
    /**
     * Process redirect (R) and permanent redirect (RP)
     *
     * @return Mage_Core_Model_Url_Rewrite_Request
     */
    protected function _processRedirectOptions()
    {
        $this->_prepareTargetPath();
        $isPermanentRedirectOption = $this->_rewrite->hasOption('RP');
        $external                  = substr($this->_rewrite->getTargetPath(), 0, 6);
        if ($external === 'http:/' || $external === 'https:') {
            $destinationStoreCode = $this->_app->getStore($this->_rewrite->getStoreId())->getCode();
            $this->_setStoreCodeCookie($destinationStoreCode);
            $this->_sendRedirectHeaders($this->_rewrite->getTargetPath(), $isPermanentRedirectOption);
        }

        $targetUrl = $this->_request->getBaseUrl() . '/' . $this->_rewrite->getTargetPath();

        $storeCode = $this->_app->getStore()->getCode();
        if (Mage::getStoreConfig('web/url/use_store') && !empty($storeCode)) {
            $targetUrl = $this->_request->getBaseUrl() . '/' . $storeCode . '/' . $this->_rewrite->getTargetPath();
        }

        if ($this->_rewrite->hasOption('R') || $isPermanentRedirectOption) {
            $this->_sendRedirectHeaders($targetUrl, $isPermanentRedirectOption);
        }

        $queryString = $this->_getQueryString();
        if ($queryString) {
            $targetUrl .= '?' . $queryString;
        }

        $this->_request->setRequestUri($targetUrl);
        $this->_request->setPathInfo($this->_rewrite->getTargetPath());

        return $this;
    }

    /**
     * Prepare path info and set params to request
     *
     * @param Mage_Core_Controller_Request_Http $request Request
     * @return string
     */
    protected function preparePathInfo($request)
    {
        $pathInfo = $request->getPathInfo();
        if ($rewritePath = Mage::helper('gomage_seobooster/layered')->getRewritePath()) {
            if ($rewritePathPos = strripos($pathInfo, '/' . $rewritePath)) {
                $queryString = substr($pathInfo, ($rewritePathPos + strlen($rewritePath) + 2));
                if ($queryString) {
                    $params   = explode('/', $queryString);
                    $separtor = Mage::helper('gomage_seobooster/layered')->getSeparator();
                    if ($separtor == '/') {
                        if (!empty($params)) {
                            while (!empty($params)) {
                                $key = array_shift($params);
                                if (!empty($params)) {
                                    $value = array_shift($params);
                                    $request->setParam($key, $value);
                                }
                            }
                        }
                    } else {
                        foreach ($params as $param) {
                            if (!$separtor) {
                                $param = explode('=', "$param=");
                                list($key, $value) = $param;
                                $request->setParam($key, $value);
                            } else {
                                $request->setParam($param, '');
                            }
                        }
                    }
                }
                $pathInfo = substr_replace($pathInfo, '', $rewritePathPos);
                if ($categorySuffix = Mage::getStoreConfig('catalog/seo/category_url_suffix')) {
                    $pathInfo .= $categorySuffix;
                }
                $request->setPathInfo($pathInfo);
            }
        }

        return $pathInfo;
    }

    /**
     * Prepare request cases.
     *
     * We have two cases of incoming paths - with and without slashes at the end ("/somepath/" and "/somepath").
     * Each of them matches two url rewrite request paths
     * - with and without slashes at the end ("/somepath/" and "/somepath").
     * Choose any matched rewrite, but in priority order that depends on same presence of slash and query params.
     *
     * @return array
     */
    protected function _getRequestCases()
    {
        $pathInfo    = $this->preparePathInfo($this->_request);
        $requestPath = trim($pathInfo, '/');
        $origSlash   = (substr($pathInfo, -1) == '/') ? '/' : '';
        // If there were final slash - add nothing to less priority paths. And vice versa.
        $altSlash = $origSlash ? '' : '/';

        $requestCases = array();
        // Query params in request, matching "path + query" has more priority
        $queryString = $this->_getQueryString();
        if ($queryString) {
            $requestCases[] = $requestPath . $origSlash . '?' . $queryString;
            $requestCases[] = $requestPath . $altSlash . '?' . $queryString;
        }
        $requestCases[] = $requestPath . $origSlash;
        $requestCases[] = $requestPath . $altSlash;
        return $requestCases;
    }

    /**
     * Prepare target path
     *
     * @return $this
     */
    protected function _prepareTargetPath()
    {
        $path = $this->_rewrite->getTargetPath();
        $this->_rewrite->setTargetPath(Mage::helper('gomage_seobooster')->addTrailingSlash($path));

        return $this;
    }

    protected function _rewriteConfig()
    {
        if (Mage::helper('gomage_seobooster')->isGoMageCheckoutEnabled()) {
            $h = Mage::helper('gomage_checkout');
            if ($h->getConfigData('general/enabled')) {
                $requestPath = trim($this->_request->getPathInfo(), '/');
                if ($requestPath == 'checkout/onepage' || $requestPath == 'checkout/onepage/index') {
                    if ($h->isAvailableWebsite() && $h->isCompatibleDevice()) {
                        $this->_request->setPathInfo('gomage_checkout/onepage');
                        return true;
                    }
                }
            }
        }
        return parent::_rewriteConfig();
    }
}
