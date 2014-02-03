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
 * Seo Booster helper
 *
 *
 * @category   GoMage
 * @package    GoMage_SeoBooster
 * @subpackage Helper
 * @author     Roman Bublik <rb@gomage.com>
 */
class GoMage_SeoBooster_Helper_Data extends Mage_Core_Helper_Data
{
    const CANONICAL_URL_DISABLED = 0;
    const CANONICAL_URL_PRODUCTS = 1;
    const CANONICAL_URL_CATEGORIES = 2;
    const CANONICAL_URL_PRODUCTS_CATEGORIES = 3;

    const CANONICAL_URL_DEFAULT_DOMAIN_VALUE = 0;

    /**
     * Return module status
     *
     * @return bool
     */
    public function isEnabled()
    {
        return Mage::getStoreConfig('gomage_seobooster/general/enabled');
    }
    public function ga()
    {
        return Zend_Json::decode(base64_decode(Mage::helper('core')->decrypt(Mage::getStoreConfig('gomage_activation/designer/ar'))));
    }

    /**
     * Return url by route
     *
     * @param string      $route   Route
     * @param array       $params  Route params
     * @param int|null    $storeId Store Id
     * @return string
     */
    public function getUrl($route, $params = array(), $storeId = null)
    {
        if (!is_null($storeId)) {
            $store = Mage::app()->getStore($storeId);
        }

        if ($this->isEnabled()) {
            $urlModel = Mage::getModel('gomage_seobooster/url');
            if (isset($store)) {
                $urlModel->setStore($store);
            }

            return $urlModel->getUrl($route, $params);
        }
        if (isset($store)) {
            return $store->getUrl($route, $params);
        }

        return $this->_getUrl($route, $params);
    }

    public function canAddTrailingSlash()
    {
        return $this->isEnabled() &&  Mage::getStoreConfig('gomage_seobooster/general/add_trailing_slash');
    }

    public function addTrailingSlash($routePath)
    {
        if ($this->canAddTrailingSlash()) {
            if ((preg_match('/\.[a-z]{2,4}$/', $routePath) === 0)
                && (substr($routePath, -1, 1) !== '/')) {
                return $routePath . '/';
            }
        }

        return $routePath;
    }

    /**
     * Can use url rewrite for product reviews
     *
     * @return bool
     */
    public function canUseProductReviewsUrlRewrite()
    {
        return $this->isEnabled() &&
            Mage::getStoreConfig('gomage_seobooster/url_rewrite/enable_product_review_url_rewrite');
    }

    /**
     * Return product reviews url rewrite path
     *
     * @return string
     */
    public function getProductReviewsUrlRewritePath()
    {
        return Mage::getStoreConfig('gomage_seobooster/url_rewrite/product_review_rewrite_path');
    }

    /**
     * Can use url rewrite for tags
     *
     * @return bool
     */
    public function canUseTagUrlRewrite()
    {
        return $this->isEnabled() &&
        Mage::getStoreConfig('gomage_seobooster/url_rewrite/enable_tag_url_rewrite');
    }

    /**
     * Return tags url rewrite path
     *
     * @return string
     */
    public function getTagRewritePath()
    {
        return Mage::getStoreConfig('gomage_seobooster/url_rewrite/tag_url_rewrite_path');
    }

    /**
     * Is rich snippet enable
     *
     * @return mixed
     */
    public function isRichSnippetEnabled()
    {
        return Mage::getStoreConfig('gomage_seobooster/general/enable_rich_snippets');
    }

    /**
     * Format Key for URL
     *
     * @param string $str
     * @return string
     */
    public function formatUrlKey($str)
    {
        $urlKey = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('catalog/product_url')->format($str));
        $urlKey = strtolower($urlKey);
        $urlKey = trim($urlKey, '-');

        return $urlKey;
    }
}
