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
 * Helper for category canonical url
 *
 * @category   GoMage
 * @package    GoMage_SeoBooster
 * @subpackage Helper
 * @author     Roman Bublik <rb@gomage.com>
 */
class GoMage_SeoBooster_Helper_Category extends GoMage_SeoBooster_Helper_Canonical_Abstract
{
    /**
     * Return can use canonical tags for categories
     *
     * @return bool
     */
    public function canUseCanonicalTag()
    {
        if (!$this->_moduleEnabled()) {
            return false;
        }

        $canonicalUrlEnabled = Mage::getStoreConfig('gomage_seobooster/general/enable_canonical_url');
        return ($canonicalUrlEnabled == GoMage_SeoBooster_Helper_Data::CANONICAL_URL_CATEGORIES)
        || ($canonicalUrlEnabled == GoMage_SeoBooster_Helper_Data::CANONICAL_URL_PRODUCTS_CATEGORIES);
    }

    /**
     * Return category canonical url
     *
     * @param Mage_catalog_Model_Category $category Category
     * @return string
     */
    public function getCanonicalUrl($category)
    {
        if ($this->canUseCanonicalTag()) {
            $storeId = $this->getCanonicalStore($category);
            if ($storeId !== GoMage_SeoBooster_Helper_Data::CANONICAL_URL_DEFAULT_DOMAIN_VALUE ||
                $storeId !== Mage::app()->getStore()->getId()) {
                $store = Mage::app()->getStore($storeId);
                if ($store->getIsActive()) {
                    $storeCategory = $this->_getCategoryInStore($category->getId(), $store->getId());
                    if ($storeCategory->getIsActive()) {
                        return $this->getCategoryUrl($storeCategory, $storeId);
                    }
                }
            }

            return $category->getUrl();
        }

        return false;
    }

    /**
     * Return store for canonical url
     *
     * @param Mage_catalog_Model_Category $category Category
     * @return int
     */
    public function getCanonicalStore($category)
    {
        $canonicalCategoryStore = $category->getCanonicalUrlStore();
        if ($canonicalCategoryStore != GoMage_SeoBooster_Helper_Data::CANONICAL_URL_DEFAULT_DOMAIN_VALUE) {
            return $canonicalCategoryStore;
        }

        return Mage::getStoreConfig('gomage_seobooster/general/cross_domain_canonical_url');
    }

    /**
     * Return category in store
     *
     * @param int $categoryId Category Id
     * @param int $storeId    Store Id
     * @return Mage_Catalog_Model_Category
     */
    protected function _getCategoryInStore($categoryId, $storeId)
    {
        return Mage::getModel('catalog/category')->setStoreId($storeId)->load($categoryId);
    }

    /**
     * Return category Url
     *
     * @param Mage_Catalog_Model_Category $category Category
     * @param int                         $storeId  Store Id
     * @return string
     */
    public function getCategoryUrl($category, $storeId = null)
    {
        $params = array('_nosid' => true, '_store_to_url' => false);
        if ($storeId) {
            $params['_store'] = $storeId;
        }
        if ($category->hasData('request_path') && $category->getRequestPath() != '') {
            return $category->getUrlInstance()->getDirectUrl($category->getRequestPath(), $params);
        }

        $rewrite = $category->getUrlRewrite();
        if ($storeId) {
            $rewrite->setStoreId($storeId);
        }
        $idPath = 'category/' . $category->getId();
        $rewrite->loadByIdPath($idPath);

        if ($rewrite->getId()) {
            $url = $category->getUrlInstance()->getDirectUrl($rewrite->getRequestPath(), $params);
        } else {
            $url = $category->getCategoryIdUrl();
        }

        $category->getUrlInstance()->setStore(Mage::app()->getStore()->getId());
        return $url;
    }
}
