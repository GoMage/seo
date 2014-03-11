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

    public function getIsAnymoreVersion($major, $minor, $revision = 0)
    {
        $version_info = Mage::getVersionInfo();

        if ($version_info['major'] > $major) {
            return true;
        } elseif ($version_info['major'] == $major) {
            if ($version_info['minor'] > $minor) {
                return true;
            } elseif ($version_info['minor'] == $minor) {
                if ($version_info['revision'] >= $revision) {
                    return true;
                }
            }
        }

        return false;
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
     * Can use rss url rewrite
     *
     * @return bool
     */
    public function canUseRssUrlRewrite()
    {
        return $this->isEnabled() &&
        Mage::getStoreConfig('gomage_seobooster/url_rewrite/enable_rss_url_rewrite');
    }

    /**
     * Return rss url rewrite path
     *
     * @return string
     */
    public function getRssUrlRewritePath()
    {
        return Mage::getStoreConfig('gomage_seobooster/url_rewrite/rss_url_rewrite_path');
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

    /**
     * Return robots for product, category or cms page
     *
     * @param Mage_Catalog_Model_Product|Mage_Catalog_Model_Category|Mage_Cms_Model_Page $entity Entity
     * @return string
     */
    public function getRobots($entity)
    {
        if (($robots = $entity->getRobots()) && $this->isEnabled()) {
            return $robots;
        }

        return Mage::getStoreConfig('design/head/default_robots');
    }

    public function getProductMaxPrice(Mage_Catalog_Model_Product $product)
    {
        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE) {
            return $this->_getConfigurableProductMaxPrice($product);
        }

        return $product->getFinalPrice();
    }

    protected function _getConfigurableProductMaxPrice(Mage_Catalog_Model_Product $product)
    {
        $productPrice = $product->getFinalPrice();
        $attributes = $product->getTypeInstance()->getConfigurableAttributes($product);
        $attributesPrice = 0.0;
        foreach ($attributes as $attribute) {
            $attributeMaxPrice = 0.0;
            if ($prices = $attribute->getPrices()) {
                foreach ($prices as $price) {
                    if ($price['is_percent']) {
                        $priceValue = $productPrice * $price['pricing_value'] / 100;
                    } else {
                        $priceValue = $price['pricing_value'];
                    }
                    $attributeMaxPrice = max($attributeMaxPrice, $priceValue);
                }
            }
            $attributesPrice+=$attributeMaxPrice;
        }

        $productPrice += $attributesPrice;
        return $productPrice;
    }

    public function getGroupedProductPrices(Mage_Catalog_Model_Product $product)
    {

        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type_Grouped::TYPE_CODE) {
            $maxPrice = 0.0;
            $minPrice = $product->getMinimalPrice();
            $associatedProducts = $product->getTypeInstance()->getAssociatedProducts($product);

            foreach ($associatedProducts as $_product) {
                $maxPrice = max($maxPrice, $_product->getFinalPrice());
                $minPrice = min($minPrice, $_product->getFinalPrice());
            }

            return new Varien_Object(array('min_price' => $minPrice, 'max_price' => $maxPrice));
        }

        return new Varien_Object();
    }
}
