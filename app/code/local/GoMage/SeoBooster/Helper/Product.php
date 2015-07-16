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
class GoMage_SeoBooster_Helper_Product extends GoMage_SeoBooster_Helper_Canonical_Abstract
{
    const CANONICAL_PRODUCT_URL_LONGEST  = 1;
    const CANONICAL_PRODUCT_URL_SHORTEST = 2;

    /**
     * Return can use canonical tags for products
     *
     * @return bool
     */
    public function canUseCanonicalUrl()
    {
        if (!$this->_moduleEnabled()) {
            return false;
        }

        $canonicalUrlEnabled = Mage::getStoreConfig('gomage_seobooster/general/enable_canonical_url');
        return ($canonicalUrlEnabled == GoMage_SeoBooster_Helper_Data::CANONICAL_URL_PRODUCTS)
        || ($canonicalUrlEnabled == GoMage_SeoBooster_Helper_Data::CANONICAL_URL_PRODUCTS_CATEGORIES);
    }

    /**
     * Return product canonical url
     *
     * @param Mage_catalog_Model_Product $product Product
     * @return string
     */
    public function getCanonicalUrl($product)
    {
        $params       = array('_nosid' => true, '_store_to_url' => false, '_ignore_category' => true, '_ignore_store' => true);
        $storeId      = $this->getCanonicalStore($product);
        $storeProduct = $product;
        if ($storeId !== GoMage_SeoBooster_Helper_Data::CANONICAL_URL_DEFAULT_DOMAIN_VALUE) {
            $store = Mage::app()->getStore($storeId);
            if ($store->getIsActive()) {
                $storeProduct = $this->_getProductInStore($product->getId(), $store->getId());
                if (Mage::helper('catalog/product')->canShow($storeProduct)) {
                    $params['_store'] = $store->getId();
                } else {
                    $storeProduct = $product;
                }
            }
        }
        if ($this->getCanonicalUrlType($storeProduct) != self::CANONICAL_PRODUCT_URL_SHORTEST) {
            $highLevelCategory = $storeProduct->getHighLevelCategory();
            if ($highLevelCategory && $highLevelCategory->getIsActive()) {
                unset($params['_ignore_category']);
                $params['category'] = $highLevelCategory->getId();
            }
        }

        return $storeProduct->getUrlModel()->getUrl($product, $params);
    }

    /**
     * Return canonical url type
     * Longest|Shortest
     *
     * @param Mage_catalog_Model_Product $product Product
     * @return int
     */
    public function getCanonicalUrlType($product)
    {
        $canonicalUrlType = $product->getCanonicalUrlType();
        if ($canonicalUrlType != GoMage_SeoBooster_Model_Catalog_Product::CANONICAL_PRODUCT_URL_TYPE_CONFIG_VALUE) {
            return $canonicalUrlType;
        }

        return Mage::getStoreConfig('gomage_seobooster/general/product_canonical_url');
    }

    /**
     * Return store for canonical url
     *
     * @param Mage_catalog_Model_Product $product Product
     * @return int
     */
    public function getCanonicalStore($product)
    {
        $canonicalProductStore = $product->getCanonicalUrlStore();
        if ($canonicalProductStore != GoMage_SeoBooster_Helper_Data::CANONICAL_URL_DEFAULT_DOMAIN_VALUE) {
            return (int)$canonicalProductStore;
        }

        return (int)Mage::getStoreConfig('gomage_seobooster/general/cross_domain_canonical_url');
    }

    /**
     * Return product in store
     *
     * @param int $productId Product Id
     * @param int $storeId Store Id
     * @return Mage_catalog_Model_Product
     */
    protected function _getProductInStore($productId, $storeId = null)
    {
        return Mage::getModel('catalog/product')->setStoreId($storeId)->load($productId);
    }
}
