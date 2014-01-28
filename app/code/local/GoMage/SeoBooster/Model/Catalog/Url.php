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
 * Catalog url model
 *
 * @category   GoMage
 * @package    GoMage_SeoBooster
 * @subpackage Model
 * @author     Roman Bublik <rb@gomage.com>
 */
class GoMage_SeoBooster_Model_Catalog_Url extends Mage_Catalog_Model_Url
{
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
        if ($product->getUrlKey() == '') {
            $urlKey = $this->getProductModel()->formatUrlKey($product->getName());
        }
        else {
            $urlKey = $this->getProductModel()->formatUrlKey($product->getUrlKey());
        }

        $idPath      = $this->generateReviewPath('id', $product, $category);
        $targetPath  = $this->generateReviewPath('target', $product, $category);
        $requestPath = $this->getProductReviewRequestPath($product, $category);

        $categoryId = null;
        $updateKeys = true;
        if ($category->getLevel() > 1) {
            $categoryId = $category->getId();
            $updateKeys = false;
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

        if ($this->getShouldSaveRewritesHistory($category->getStoreId())) {
            $this->_saveRewriteHistory($rewriteData, $this->_rewrite);
        }

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
    public function generateReviewPath($type = 'target', $product = null, $category = null)
    {
        if (!$product && !$category) {
            Mage::throwException(Mage::helper('core')->__('Please specify either a category or a product, or both.'));
        }

        // generate id_path
        if ('id' === $type) {
            if ($category && $category->getLevel() > 1) {
                return 'product/review/' . $product->getId() . '/' . $category->getId();
            }
            return 'product/review/' . $product->getId();
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
            $productUrlSuffix  = $this->getProductUrlSuffix($category->getStoreId());
            $reviewRewritePath = $this->_getProductReviewsRewritePath();

            if ($category->getLevel() > 1) {
                // To ensure, that category has url path either from attribute or generated now
                $this->_addCategoryUrlPath($category);
                $categoryUrl = Mage::helper('catalog/category')->getCategoryUrlPath($category->getUrlPath(),
                    false, $category->getStoreId());
                $path = $categoryUrl . '/' . $urlKey . '/' . $reviewRewritePath .$productUrlSuffix;
                return $this->getUnusedPath($category->getStoreId(), $path, $this->generatePath('id', $product, $category));
            }

            // for product only
            $path = $urlKey . '/' . $reviewRewritePath . $productUrlSuffix;
            return $this->getUnusedPath($category->getStoreId(), $path,
                $this->generatePath('id', $product)
            );
        }

        if ($category && $category->getLevel() > 1) {
            return 'review/product/list/id/' . $product->getId() . '/category/' . $category->getId();
        }
        return 'review/product/list/id/' . $product->getId();
    }

    /**
     * Get unique product reviews request path
     *
     * @param   Varien_Object $product  Paroduct
     * @param   Varien_Object $category Category
     * @return  string
     */
    public function getProductReviewRequestPath($product, $category)
    {
        if ($product->getUrlKey() == '') {
            $urlKey = $this->getProductModel()->formatUrlKey($product->getName());
        } else {
            $urlKey = $this->getProductModel()->formatUrlKey($product->getUrlKey());
        }
        $storeId = $category->getStoreId();
        $suffix  = $this->getProductUrlSuffix($storeId);
        $idPath  = $this->generateReviewPath('id', $product, $category);
        $reviewRewritePath = $this->_getProductReviewsRewritePath();

        /**
         * Prepare product base request path
         */
        if ($category->getLevel() > 1) {
            // To ensure, that category has path either from attribute or generated now
            $this->_addCategoryUrlPath($category);
            $categoryUrl = Mage::helper('catalog/category')->getCategoryUrlPath($category->getUrlPath(),
                false, $storeId);
            $requestPath = $categoryUrl . '/' . $urlKey;
        } else {
            $requestPath = $urlKey;
        }

        if (strlen($requestPath) > self::MAX_REQUEST_PATH_LENGTH + self::ALLOWED_REQUEST_PATH_OVERFLOW) {
            $requestPath = substr($requestPath, 0, self::MAX_REQUEST_PATH_LENGTH);
        }

        $requestPath = $requestPath . '/' . $reviewRewritePath;

        $this->_rewrite = null;
        /**
         * Check $requestPath should be unique
         */
        if (isset($this->_rewrites[$idPath])) {
            $this->_rewrite = $this->_rewrites[$idPath];
            $existingRequestPath = $this->_rewrites[$idPath]->getRequestPath();

            if ($existingRequestPath == $requestPath . $suffix) {
                return $existingRequestPath;
            }

            $existingRequestPath = preg_replace('/' . preg_quote($suffix, '/') . '$/', '', $existingRequestPath);
            /**
             * Check if existing request past can be used
             */
            if ($product->getUrlKey() == '' && !empty($requestPath)
                && strpos($existingRequestPath, $requestPath) === 0
            ) {
                $existingRequestPath = preg_replace(
                    '/^' . preg_quote($requestPath, '/') . '/', '', $existingRequestPath
                );
                if (preg_match('#^-([0-9]+)$#i', $existingRequestPath)) {
                    return $this->_rewrites[$idPath]->getRequestPath();
                }
            }

            $fullPath = $requestPath.$suffix;
            if ($this->_deleteOldTargetPath($fullPath, $idPath, $storeId)) {
                return $fullPath;
            }
        }
        /**
         * Check 2 variants: $requestPath and $requestPath . '-' . $productId
         */
        $validatedPath = $this->getResource()->checkRequestPaths(
            array($requestPath.$suffix, $requestPath.'-'.$product->getId().$suffix),
            $storeId
        );

        if ($validatedPath) {
            return $validatedPath;
        }
        /**
         * Use unique path generator
         */
        return $this->getUnusedPath($storeId, $requestPath.$suffix, $idPath);
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
}
