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
class GoMage_SeoBooster_Model_Catalog_Layer_Url extends Mage_Core_Model_Url
{
    /**
     * Build url by requested path and parameters
     *
     * @param   string|null $routePath Route path
     * @param   array|null $routeParams Route params
     * @return  string
     */
    public function getUrl($routePath = null, $routeParams = null)
    {
        $escapeQuery = false;

        /**
         * All system params should be unset before we call getRouteUrl
         * this method has condition for adding default controller and action names
         * in case when we have params
         */
        if (Mage::helper('gomage_seobooster')->isEnabled()) {
            if (isset($routeParams['_direct'])) {
                $routeParams['_direct'] = $this->_addTrailingSlash($routeParams['_direct']);
            }
        }

        if (isset($routeParams['_fragment'])) {
            $this->setFragment($routeParams['_fragment']);
            unset($routeParams['_fragment']);
        }

        if (isset($routeParams['_escape'])) {
            $escapeQuery = $routeParams['_escape'];
            unset($routeParams['_escape']);
        }

        $query = null;
        if (isset($routeParams['_query'])) {
            $this->purgeQueryParams();
            $query = $routeParams['_query'];
            unset($routeParams['_query']);
        }

        $layeredQueryParams = null;
        if (isset($routeParams['_layered_query_params'])) {
            $layeredQueryParams = $routeParams['_layered_query_params'];
            unset($routeParams['_layered_query_params']);
        }

        $noSid = null;
        if (isset($routeParams['_nosid'])) {
            $noSid = (bool)$routeParams['_nosid'];
            unset($routeParams['_nosid']);
        }

        if (isset($routeParams['_use_layer_rewrite'])) {
            $this->setData('use_layer_rewrite', $routeParams['_use_layer_rewrite']);
            unset($routeParams['_use_layer_rewrite']);
        }

        $url = $this->getRouteUrl($routePath, $routeParams);
        /**
         * Apply query params, need call after getRouteUrl for rewrite _current values
         */
        if ($query !== null) {
            if (is_string($query)) {
                $this->setQuery($query);
            } elseif (is_array($query)) {
                $this->setQueryParams($query, !empty($routeParams['_current']));
            }
            if ($query === false) {
                $this->setQueryParams(array());
            }
        }

        if ($layeredQueryParams !== null) {
            $this->setLayeredQueryParams($layeredQueryParams);
        }

        if ($noSid !== true) {
            $this->_prepareSessionUrl($url);
        }

        if ($this->_getData('layered_query_params')) {
            $url = $this->_addLayerRewritePathToRoute($url);
        }
        $url = $this->_addQueryToUrl($url, $escapeQuery);

        if ($this->getFragment()) {
            $url .= '#' . $this->getFragment();
        }

        return $this->escape($url);
    }

    /**
     * Add query string to url
     *
     * @param string $url Url
     * @param bool $escapeQuery Escape Query
     * @return string
     */
    protected function _addQueryToUrl($url, $escapeQuery = false)
    {
        $mark = (strpos($url, '?') === false) ? '?' : ($escapeQuery ? '&amp;' : '&');

        if ($layeredQueryParams = $this->_getData('layered_query_params')) {
            if ($layeredQuery = Mage::helper('gomage_seobooster/layered')->getQuery($layeredQueryParams, $escapeQuery)) {
                if (Mage::helper('gomage_seobooster/layered')->getRewritePath()
                    && $this->_getData('use_layer_rewrite') !== false
                ) {
                    if (Mage::registry('current_category') && ($categorySuffix = Mage::getStoreConfig('catalog/seo/category_url_suffix'))) {
                        $layeredQuery .= $categorySuffix;
                    }
                    $url .= $layeredQuery;
                } else {
                    $url .= $mark . $layeredQuery;
                    $mark = $escapeQuery ? '&amp;' : '&';
                }
            }
        }

        $query = $this->getQuery($escapeQuery);
        if ($query) {
            $url .= $mark . $query;
        }

        $url = $this->_addTrailingSlash($url);
        return $url;
    }

    /**
     * Set Layer query params
     *
     * @param array $params Params
     * @return $this
     */
    public function setLayeredQueryParams(array $params)
    {
        $data = $this->_getData('layered_query_params');
        if (!$data) {
            $data = array();
        }
        if (is_array($params)) {
            foreach ($params as $key => $value) {
                $data[$key] = $value;
            }
            $this->setData('layered_query_params', $data);
        }

        return $this;
    }

    /**
     * Set layer query param
     *
     * @param string $key Key
     * @param mixed $value Value
     * @return $this
     */
    public function setLayerQueryParam($key, $value = null)
    {
        $params = $this->_getData('layered_query_params');
        if (is_null($value)) {
            $param = Mage::helper('gomage_seobooster/layered')->prepareLayeredQueryParam($key);
            if (count($param) < 2) {
                return $this;
            }
            list($key, $value) = $param;
        }
        $params[$key] = $value;
        $this->setLayeredQueryParams($params);
        return $this;
    }

    /**
     * Retrieve route path
     *
     * @param array $routeParams Route Params
     * @return string
     */
    public function getRoutePath($routeParams = array())
    {
        if (!Mage::helper('gomage_seobooster')->isEnabled()) {
            return parent::getRoutePath($routeParams);
        }

        if (!$this->hasData('route_path')) {
            $routePath = $this->getRequest()->getAlias(Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS);
            if (!empty($routeParams['_use_rewrite']) && ($routePath !== null)) {
                $routePath = $this->_addTrailingSlash($routePath);
                $this->setData('route_path', $routePath);
                return $routePath;
            }
            $routePath = $this->getActionPath();

            if ($this->getRouteParams()) {
                foreach ($this->getRouteParams() as $key => $value) {
                    if (is_null($value) || false === $value || '' === $value || !is_scalar($value)) {
                        continue;
                    }
                    $routePath .= $key . '/' . $value . '/';
                }
            }
            if ($routePath != '' && substr($routePath, -1, 1) !== '/') {
                $routePath .= '/';
            }
            $this->setData('route_path', $routePath);
        }
        return $this->_getData('route_path');
    }

    /**
     * Add Layer rewrite path to route path
     *
     * @param string $routePath Route Path
     * @return string
     */
    protected function _addLayerRewritePathToRoute($routePath)
    {
        if (($layerRewritePath = Mage::helper('gomage_seobooster/layered')->getRewritePath())
            && ($this->_getData('use_layer_rewrite') !== false)
        ) {
            if ($routePath != '' && substr($routePath, -1, 1) !== '/') {
                $routePath = preg_replace('/\.[a-z]{2,4}$/', '', $routePath);
                $routePath .= '/';
            }

            $routePath .= $layerRewritePath . '/';
        }

        return $routePath;
    }

    /**
     * Set route params
     *
     * @param array $data Data
     * @param boolean $unsetOldParams Unset old params
     * @return Mage_Core_Model_Url
     */
    public function setRouteParams(array $data, $unsetOldParams = true)
    {
        if (isset($data['_type'])) {
            $this->setType($data['_type']);
            unset($data['_type']);
        }

        if (isset($data['_store'])) {
            $this->setStore($data['_store']);
            unset($data['_store']);
        }

        if (isset($data['_forced_secure'])) {
            $this->setSecure((bool)$data['_forced_secure']);
            $this->setSecureIsForced(true);
            unset($data['_forced_secure']);
        } elseif (isset($data['_secure'])) {
            $this->setSecure((bool)$data['_secure']);
            unset($data['_secure']);
        }

        if (isset($data['_absolute'])) {
            unset($data['_absolute']);
        }

        if ($unsetOldParams) {
            $this->unsetData('route_params');
        }

        $this->setUseUrlCache(true);
        if (isset($data['_current'])) {
            if (is_array($data['_current'])) {
                foreach ($data['_current'] as $key) {
                    if (array_key_exists($key, $data) || !$this->getRequest()->getUserParam($key)) {
                        continue;
                    }
                    $data[$key] = $this->getRequest()->getUserParam($key);
                }
            } elseif ($data['_current']) {
                foreach ($this->getRequest()->getUserParams() as $key => $value) {
                    if (array_key_exists($key, $data) || $this->getRouteParam($key)) {
                        continue;
                    }
                    $data[$key] = $value;
                }
                foreach ($this->getRequest()->getQuery() as $key => $value) {
                    if (!Mage::helper('gomage_seobooster/layered')->isLayeredQueryParam($key, $value)) {
                        $this->setQueryParam($key, $value);
                    } else {
                        $this->setLayerQueryParam($key);
                    }
                }

                if (Mage::helper('gomage_seobooster/layered')->getRewritePath()) {
                    $layeredParams = Mage::helper('gomage_seobooster/layered')->getFilterableParams();
                    foreach ($layeredParams as $key => $value) {
                        $this->setLayerQueryParam($key, $value);
                    }
                }

                $this->setUseUrlCache(false);
            }
            unset($data['_current']);
        }

        if (isset($data['_use_rewrite'])) {
            unset($data['_use_rewrite']);
        }

        if (isset($data['_store_to_url']) && (bool)$data['_store_to_url'] === true) {
            if (!Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_STORE_IN_URL, $this->getStore())
                && !Mage::app()->isSingleStoreMode()
            ) {
                $this->setQueryParam('___store', $this->getStore()->getCode());
            }
        }
        unset($data['_store_to_url']);

        foreach ($data as $k => $v) {
            $this->setRouteParam($k, $v);
        }

        return $this;
    }

    /**
     * Add trailing slash to route path if needed
     *
     * @param string $routePath Route path
     * @return string
     */
    protected function _addTrailingSlash($routePath)
    {
        return Mage::helper('gomage_seobooster')->addTrailingSlash($routePath);
    }
}
