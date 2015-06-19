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
class GoMage_SeoBooster_Helper_Category extends GoMage_SeoBooster_Helper_Canonical_Abstract
{
    /**
     * Return can use canonical tags for categories
     *
     * @return bool
     */
    public function canUseCanonicalUrl()
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
        $storeId = $this->getCanonicalStore($category);

        if ($storeId !== GoMage_SeoBooster_Helper_Data::CANONICAL_URL_DEFAULT_DOMAIN_VALUE ||
            $storeId !== Mage::app()->getStore()->getId()
        ) {
            $store = Mage::app()->getStore($storeId);
			
            if ($store->getId() && $store->getIsActive()) {
                $storeCategory = $this->_getCategoryInStore($category->getId(), $store->getId());

                if ($storeCategory->getIsActive()) {
                    return $this->getCategoryUrl($storeCategory, $storeId);
                }
            }
        }

        return $category->getUrl();
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
     * @param int $storeId Store Id
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
     * @param int $storeId Store Id
     * @return string
     */
    public function getCategoryUrl($category, $storeId = null)
    {
        $params = array('_nosid' => true);
        if ($storeId) {
            $params['_store']        = $storeId;
            $params['_store_to_url'] = true;
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

    /**
     * Enable link rel
     *
     * @return bool
     */
    public function canAddNextPrevLinkRel()
    {
        return $this->_moduleEnabled() && Mage::getStoreConfig('gomage_seobooster/general/enable_next_prev_link_rel');
    }

    /**
     * Add Link Rel next|prev
     *
     * @return GoMage_SeoBooster_Helper_Category
     */
    public function addNextPrevLinkRel()
    {
        $headBlock = Mage::app()->getLayout()->getBlock('head');
        if ($pager = $this->_getPagerBlock()) {
            if ($pager->getLastPageNum() > 1) {
                if ($pager->isLastPage() || $pager->getCurrentPage() > 1) {
                    $lastPageNum = $pager->getLastPageNum() < $pager->getCurrentPage()
                        ? $pager->getLastPageNum() : $pager->getCurrentPage();
                    $params      = array(
                        $pager->getLimitVarName() => $pager->getLimit(),
                        $pager->getModeVarName()  => $pager->getMode(),
                        $pager->getPageVarName()  => ($lastPageNum - 1 > 1) ? $lastPageNum - 1 : null
                    );
                    $headBlock->addLinkRel('prev', $pager->getPagerUrl($params));
                }
                if ($pager->isFirstPage() || $pager->getCurrentPage() < $pager->getLastPageNum()) {
                    $headBlock->addLinkRel('next', $pager->getPagerUrl(array(
                            $pager->getLimitVarName() => $pager->getLimit(),
                            $pager->getModeVarName()  => $pager->getMode(),
                            $pager->getPageVarName()  => $pager->getCurrentPage() + 1
                        )
                    )
                    );
                }
            }
        }

        return $this;
    }

    /**
     * Return pager block
     *
     * @return bool|Mage_Page_Block_Html_Pager
     */
    protected function _getPagerBlock()
    {
        $toolbarBlock = Mage::app()->getLayout()->createBlock('catalog/product_list_toolbar');
        $pagerBlock   = Mage::app()->getLayout()->createBlock('page/html_pager');
        $toolbarBlock->setCollection($this->_getCategoryProducts());

        if ($pagerBlock instanceof Varien_Object) {
            $pagerBlock->setAvailableLimit($toolbarBlock->getAvailableLimit());
            $pagerBlock->setLimit($toolbarBlock->getLimit())
                ->setLimitVarName($toolbarBlock->getLimitVarName())
                ->setPageVarName($toolbarBlock->getPageVarName())
                ->setModeVarName($toolbarBlock->getModeVarName())
                ->setMode($toolbarBlock->getCurrentMode())
                ->setCollection($toolbarBlock->getCollection());

            return $pagerBlock;
        }

        return false;
    }

    /**
     * Return product collection
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _getCategoryProducts()
    {
        $layer = Mage::getSingleton('catalog/layer');
        return $layer->getProductCollection();
    }


}
