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

class GoMage_SeoBooster_Model_Url extends Mage_Core_Model_Url
{
    /**
     * Build url by requested path and parameters
     *
     * @param   string|null $routePath   Route path
     * @param   array|null  $routeParams Route params
     * @return  string
     */
    public function getUrl($routePath = null, $routeParams = null)
    {
        if (Mage::helper('gomage_seobooster')->isEnabled()) {
            if (isset($routeParams['_direct'])) {
                $routeParams['_direct'] = $this->_addTrailingSlash($routeParams['_direct']);
            }
        }

        return parent::getUrl($routePath, $routeParams);
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
                foreach ($this->getRouteParams() as $key=>$value) {
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
