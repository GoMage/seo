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

class GoMage_SeoBooster_Model_Catalog_Product_Url extends Mage_Catalog_Model_Product_Url
{
    /**
     * Retrieve URL Instance
     *
     * @return Mage_Core_Model_Url
     */
    public function getUrlInstance()
    {
        if (!$this->_url) {
            $this->_url = Mage::getModel('gomage_seobooster/url');
        }
        return $this->_url;
    }

    /**
     * Retrieve Product URL using UrlDataObject
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $params
     * @return string
     */
    public function getUrl(Mage_Catalog_Model_Product $product, $params = array())
    {
        $url = $product->getData('url');
        if (!empty($url)) {
            return $url;
        }

        $requestPath = $product->getData('request_path');
        if (empty($requestPath)) {
            $requestPath = $this->_getRequestPath($product, $this->_getCategoryIdForUrl($product, $params));
            $product->setRequestPath($requestPath);
        }

        if (isset($params['_store'])) {
            $storeId = $this->_getStoreId($params['_store']);
        } else {
            $storeId = $product->getStoreId();
        }

        if ($storeId != $this->_getStoreId() && $storeId != null && !isset($routeParams['_ignore_store'])) {
            $params['_store_to_url'] = true;
        }

        // reset cached URL instance GET query params
        if (!isset($params['_query'])) {
            $params['_query'] = array();
        }
        $this->getUrlInstance()->setStore($storeId);
        $productUrl = $this->_getProductUrl($product, $requestPath, $params);
        $product->setData('url', $productUrl);
        return $product->getData('url');
    }

    /**
     * Retrieve request path
     *
     * @param Mage_Catalog_Model_Product $product
     * @param int $categoryId
     * @return bool|string
     */
    protected function _getRequestPath($product, $categoryId)
    {
        $idPath = sprintf('product/%d', $product->getEntityId());
        if ($categoryId) {
            $idPath = sprintf('%s/%d', $idPath, $categoryId);
        }
        $rewrite = $this->getUrlRewrite();
        $rewrite->setStoreId($product->getStoreId())
            ->loadByIdPath($idPath);
        if ($rewrite->getId()) {
            return $rewrite->getRequestPath();
        }

        return false;
    }

    /**
     * Check product category
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $params
     *
     * @return int|null
     */
    protected function _getCategoryIdForUrl($product, $params)
    {
        if (isset($params['_ignore_category'])) {
            return null;
        } else {
            return isset($params['category']) ? $params['category'] : ($product->getCategoryId() && !$product->getDoNotUseCategoryId()
                ? $product->getCategoryId() : null);
        }
    }

    /**
     * Returns checked store_id value
     *
     * @param int|null $id
     * @return int
     */
    protected function _getStoreId($id = null)
    {
        return Mage::app()->getStore($id)->getId();
    }

    /**
     * Retrieve product URL based on requestPath param
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $requestPath
     * @param array $routeParams
     *
     * @return string
     */
    protected function _getProductUrl($product, $requestPath, $routeParams)
    {
        if (!empty($requestPath)) {
            return $this->getUrlInstance()->getDirectUrl($requestPath, $routeParams);
        }
        $routeParams['id'] = $product->getId();
        $routeParams['s'] = $product->getUrlKey();
        $categoryId = $this->_getCategoryIdForUrl($product, $routeParams);
        if ($categoryId) {
            $routeParams['category'] = $categoryId;
        }
        return $this->getUrlInstance()->getUrl('catalog/product/view', $routeParams);
    }

    public function getProductReviewsUrl(Mage_Catalog_Model_Product $product, $params = array())
    {
        $routePath      = '';
        $routeParams    = $params;

        $storeId    = $product->getStoreId();
        if (isset($params['_ignore_category'])) {
            unset($params['_ignore_category']);
            $categoryId = null;
        } else {
            $categoryIds = $product->getCategoryIds();
            $categoryId = $product->getCategoryId() && !$product->getDoNotUseCategoryId()
            && (in_array($product->getCategoryId(), $categoryIds))
                ? $product->getCategoryId() : null;
        }

        $idPath = sprintf('product/reviews/%d', $product->getEntityId());
        if ($categoryId) {
            $idPath = sprintf('%s/%d', $idPath, $categoryId);
        }

        $rewrite = $this->getUrlRewrite();
        $rewrite->setStoreId($storeId)
            ->loadByIdPath($idPath);
        if ($rewrite->getId()) {
            $requestPath = $rewrite->getRequestPath();
        }

        if (isset($routeParams['_store'])) {
            $storeId = Mage::app()->getStore($routeParams['_store'])->getId();
        }

        if ($storeId != Mage::app()->getStore()->getId()) {
            $routeParams['_store_to_url'] = true;
        }

        if (!empty($requestPath)) {
            $routeParams['_direct'] = $requestPath;
        } else {
            $routePath = 'review/product/list';
            $routeParams['id']  = $product->getId();
            if ($categoryId) {
                $routeParams['category'] = $categoryId;
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