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
 * Rss category url
 *
 * @category   GoMage
 * @package    GoMage_SeoBooster
 * @subpackage Model
 * @author     Roman Bublik <rb@gomage.com>
 */
class GoMage_SeoBooster_Model_Rss_Url
{
    /**
     * Static URL Rewrite Instance
     *
     * @var Mage_Core_Model_Url_Rewrite
     */
    protected static $_urlRewrite;

    /**
     * Static URL instance
     *
     * @var Mage_Core_Model_Url
     */
    protected static $_url;

    /**
     * Retrieve URL Instance
     *
     * @return Mage_Core_Model_Url
     */
    public function getUrlInstance()
    {
        if (!self::$_url) {
            self::$_url = Mage::getModel('gomage_seobooster/url');
        }
        return self::$_url;
    }

    /**
     * Retrieve URL Rewrite Instance
     *
     * @return Mage_Core_Model_Url_Rewrite
     */
    public function getUrlRewrite()
    {
        if (!self::$_urlRewrite) {
            self::$_urlRewrite = Mage::getModel('core/url_rewrite');
        }
        return self::$_urlRewrite;
    }

    /**
     * Return rss url
     * @param int   $categoryId Category Id
     * @param int   $storeId    Store Id
     * @param array $params     Params
     * @return string
     */
    public function getUrl($categoryId, $storeId = null, $params = array())
    {
        $routePath      = '';
        $routeParams    = $params;

        $idPath = sprintf('rss/cid/%d', $categoryId);

        $rewrite = $this->getUrlRewrite();
        $rewrite->loadByIdPath($idPath);
        if ($rewrite->getId()) {
            $requestPath = $rewrite->getRequestPath();
        }

        if (!empty($requestPath)) {
            $routeParams['_direct'] = $requestPath;
        } else {
            $routePath = 'rss/catalog/category/cid/' . $categoryId;
            if ($storeId) {
                $routePath .= '/store_id/' . $storeId;
            }
        }

        // reset cached URL instance GET query params
        if (!isset($routeParams['_query'])) {
            $routeParams['_query'] = array();
        }

        return $this->getUrlInstance()->setStore($storeId)
            ->getUrl($routePath, $routeParams);
    }
}
