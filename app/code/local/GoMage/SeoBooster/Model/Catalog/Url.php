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

class GoMage_SeoBooster_Model_Catalog_Url extends Mage_Catalog_Model_Url
{
    /**
     * Retrieve resource model
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Url
     */
    public function getResource()
    {
        if (is_null($this->_resourceModel)) {
            $this->_resourceModel = Mage::getResourceModel('gomage_seobooster/catalog_url');
        }
        return $this->_resourceModel;
    }

    /**
     * Refresh all rewrite urls for some store or for all stores
     * Used to make full reindexing of url rewrites
     *
     * @param int $storeId
     * @return Mage_Catalog_Model_Url
     */
    public function refreshRewrites($storeId = null)
    {
        if (is_null($storeId)) {
            foreach ($this->getStores() as $store) {
                $this->refreshRewrites($store->getId());
            }
            return $this;
        }

        $this->clearStoreInvalidRewrites($storeId);
        $this->refreshCategoryRewrite($this->getStores($storeId)->getRootCategoryId(), $storeId, false);
        $this->refreshProductRewrites($storeId);
        if (Mage::helper('gomage_seobooster')->canUseProductReviewsUrlRewrite()) {
            Mage::getModel('gomage_seobooster/review_url')->refreshReviewsRewrites($storeId);
        }
        $this->getResource()->clearCategoryProduct($storeId);

        return $this;
    }

    /**
     * Refresh product rewrite
     *
     * @param Varien_Object $product
     * @param Varien_Object $category
     * @return Mage_Catalog_Model_Url
     */
    protected function _refreshProductRewrite(Varien_Object $product, Varien_Object $category)
    {
        parent::_refreshProductRewrite($product, $category);
        if (Mage::helper('gomage_seobooster')->canUseProductReviewsUrlRewrite()) {
            $this->_refreshProductReviewsRewrite($product, $category);
        }

        return $this;
    }

    /**
     * Refresh product reviews url rewrite
     *
     * @param Varien_Object $product  Product
     * @param Varien_Object $category Category
     * @return $this
     */
    protected function _refreshProductReviewsRewrite(Varien_Object $product, Varien_Object $category)
    {
        if ($category->getId() == $category->getPath()) {
            return $this;
        }

        $idPath      = $this->generateReviewsPath('id', $product, $category);
        $targetPath  = $this->generateReviewsPath('target', $product, $category);
        $requestPath = $this->generateReviewsPath('request', $product, $category);

        $categoryId = null;
        if ($category->getLevel() > 1) {
            $categoryId = $category->getId();
        }

        $rewriteData = array(
            'store_id'      => $category->getStoreId(),
            'category_id'   => $categoryId,
            'product_id'    => $product->getId(),
            'id_path'       => $idPath,
            'request_path'  => $requestPath,
            'target_path'   => $targetPath,
            'is_system'     => 0
        );

        $this->getResource()->saveRewrite($rewriteData, $this->_rewrite);

        return $this;
    }

    /**
     * Generate either id path, request path or target path for product reviews
     *
     * For generating id or system path, either product or category is required
     * For generating request path - category is required
     *
     * @param string $type
     * @param Varien_Object $product
     * @param Varien_Object $category
     * @return string
     * @throws Mage_Core_Exception
     */
    public function generateReviewsPath($type = 'target', $product = null, $category = null)
    {
        if (!$product && !$category) {
            Mage::throwException(Mage::helper('core')->__('Please specify either a category or a product, or both.'));
        }

        // generate id_path
        if ('id' === $type) {
            if ($category && $category->getLevel() > 1) {
                return 'product/reviews/' . $product->getId() . '/' . $category->getId();
            }
            return 'product/reviews/' . $product->getId();
        }

        // generate request_path
        if ('request' === $type) {
            // for product & category
            if (!$category) {
                Mage::throwException(Mage::helper('core')->__('A category object is required for determining the product request path.')); // why?
            }

            if ($product->getUrlKey() == '') {
                $urlKey = $this->getProductModel()->formatUrlKey($product->getName());
            }
            else {
                $urlKey = $this->getProductModel()->formatUrlKey($product->getUrlKey());
            }
            $productUrlSuffix  = '.html';
            $reviewRewritePath = $this->_getProductReviewsRewritePath();

            if ($category->getLevel() > 1) {
                // To ensure, that category has url path either from attribute or generated now
                $this->_addCategoryUrlPath($category);
                $categoryUrl = Mage::helper('catalog/category')->getCategoryUrlPath($category->getUrlPath(),
                    false, $category->getStoreId());
                $path = $categoryUrl . '/' . $urlKey . '/' . $reviewRewritePath .$productUrlSuffix;
                return $this->getUnusedPath($category->getStoreId(), $path, $this->generateReviewsPath('id', $product, $category));
            }

            // for product only
            $path = $urlKey . '/' . $reviewRewritePath . $productUrlSuffix;
            return $this->getUnusedPath($category->getStoreId(), $path,
                $this->generateReviewsPath('id', $product)
            );
        }

        if ($category && $category->getLevel() > 1) {
            return 'review/product/list/id/' . $product->getId() . '/category/' . $category->getId();
        }
        return 'review/product/list/id/' . $product->getId();
    }

    /**
     * Return product reviews rewrite path
     *
     * @return string
     */
    protected function _getProductReviewsRewritePath()
    {
        return Mage::helper('gomage_seobooster')->getProductReviewsUrlRewritePath();
    }

    /**
     * Refresh category rewrite
     *
     * @param Varien_Object $category
     * @param string $parentPath
     * @param bool $refreshProducts
     * @return Mage_Catalog_Model_Url
     */
    protected function _refreshCategoryRewrites(Varien_Object $category, $parentPath = null, $refreshProducts = true)
    {
        parent::_refreshCategoryRewrites($category, $parentPath, $refreshProducts);
        if (Mage::helper('gomage_seobooster')->canUseRssUrlRewrite()) {
            $this->_refreshCategoryRssRewrite($category);
        }

        return $this;
    }

    /**
     * Refresh category rss rewrite
     *
     * @param Varien_Object $category Category
     * @return $this
     */
    protected function _refreshCategoryRssRewrite(Varien_Object $category)
    {
        if ($category->getId() != $this->getStores($category->getStoreId())->getRootCategoryId()) {
            $idPath      = $this->generateRssPath('id', $category);
            $targetPath  = $this->generateRssPath('target', $category);
            $requestPath = $this->generateRssPath('request', $category);

            $rewriteData = array(
                'store_id'      => $category->getStoreId(),
                'category_id'   => $category->getId(),
                'product_id'    => null,
                'id_path'       => $idPath,
                'request_path'  => $requestPath,
                'target_path'   => $targetPath,
                'is_system'     => 0
            );

            $this->getResource()->saveRewrite($rewriteData, $this->_rewrite);
        }
        return $this;
    }

    /**
     * Generate either id path, request path or target path for rss
     *
     * For generating id or system path, either product or category is required
     * For generating request path - category is required
     *
     * @param string $type
     * @param Varien_Object $category
     * @return string
     * @throws Mage_Core_Exception
     */
    public function generateRssPath($type = 'target', $category = null)
    {
        if (!$category) {
            Mage::throwException(Mage::helper('core')->__('Please specify either a category.'));
        }

        // generate id_path
        if ('id' === $type) {
            return 'rss/cid/' . $category->getId();
        } elseif ('request' === $type) {
            $urlKey = $this->getCategoryModel()->formatUrlKey($category->getUrlKey());
            $categoryUrlSuffix = $this->getCategoryUrlSuffix($category->getStoreId());
            $rssRewritePath = Mage::helper('gomage_seobooster')->getRssUrlRewritePath();

            return $rssRewritePath. '/' . $urlKey . $categoryUrlSuffix;
        }

        return 'rss/catalog/category/cid/'. $category->getId() . '/store_id/' . $category->getStoreId();
    }


}
