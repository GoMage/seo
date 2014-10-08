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
class GoMage_SeoBooster_Model_Review_Url
{
    protected $_reviewUrlSuffix = '.html';

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

    public function refreshReviewRewrite(Mage_Review_Model_Review $review, $storeId = null)
    {
        if (!$review || !$review->getId()) {
            return $this;
        }

        $product     = $this->_getProduct($review);
        $idPath      = $this->generatePath('id', $review);
        $targetPath  = $this->generatePath('target', $review);
        $requestPath = $this->generatePath('request', $review, $product);

        $rewriteData = array(
            'product_id'   => $product->getId(),
            'store_id'     => $storeId ? $storeId : $review->getStoreId(),
            'id_path'      => $idPath,
            'request_path' => $requestPath,
            'target_path'  => $targetPath,
            'is_system'    => 0
        );

        $urlRewriteModel = Mage::getModel('core/url_rewrite');
        if ($urlRewriteId = $review->getUrlRewriteId()) {
            $urlRewriteModel->load($urlRewriteId);
            if ($urlRewriteModel->getId()) {
                if ($review->isDeleted()) {
                    $urlRewriteModel->delete();
                    return $this;
                }

                $urlRewriteModel->addData($rewriteData);
                $urlRewriteModel->save();
            }
        } else {
            $urlRewriteModel->setData($rewriteData)->save();
            $review->setData('url_rewrite_id', $urlRewriteModel->getId());
            $review->save();
        }

        return $this;

    }

    protected function _getProduct($review)
    {
        if ($product = Mage::registry('current_product')) {
            return $product;
        }
        $productId = $review->getEntityPkValue();
        $product   = Mage::getModel('catalog/product')->load($productId);

        return $product;

    }

    public function generatePath($type = 'target', $review, $product = null)
    {
        if (!$review || !$review->getId()) {
            Mage::throwException(Mage::helper('gomage_seobooster')->__('Please specify review.'));
        }

        if ($type == 'id') {
            return 'product/review/' . $review->getId();
        } elseif ($type == 'request') {


            if ($product->getUrlKey() == '') {
                $productUrlKey = $this->_formatUrlKey($product->getName());
            } else {
                $productUrlKey = $this->_formatUrlKey($product->getUrlKey());
            }

            return $productUrlKey . '/' . $this->_getProductReviewsRewritePath() . '/' . $review->getId() . $this->_reviewUrlSuffix;
        }

        return 'review/product/view/id/' . $review->getId();
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
     * Format Key for URL
     *
     * @param string $str
     * @return string
     */
    protected function _formatUrlKey($str)
    {
        return Mage::helper('gomage_seobooster')->formatUrlKey($str);
    }

    public function refreshReviewsRewrites($storeId)
    {
        $reviews = Mage::getModel('review/review')->getCollection();
        if ($storeId) {
            $reviews->addStoreFilter($storeId);
        }
        foreach ($reviews as $review) {
            if (!$review || !$review->getId()) {
                continue;
            }

            $product     = $this->_getProduct($review);
            $idPath      = $this->generatePath('id', $review);
            $targetPath  = $this->generatePath('target', $review);
            $requestPath = $this->generatePath('request', $review, $product);

            $rewriteData = array(
                'product_id'   => $product->getId(),
                'store_id'     => $storeId ? $storeId : $review->getStoreId(),
                'id_path'      => $idPath,
                'request_path' => $requestPath,
                'target_path'  => $targetPath,
                'is_system'    => 0
            );

            Mage::getResourceModel('gomage_seobooster/catalog_url')->saveRewrite($rewriteData, null);
        }

        return $this;
    }

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

    public function getUrl($reviewId, $params = array())
    {
        $routePath   = '';
        $routeParams = $params;
        $storeId     = null;

        $idPath = sprintf('product/review/%d', $reviewId);

        $rewrite = $this->getUrlRewrite();
        $rewrite->loadByIdPath($idPath);
        if ($rewrite->getId()) {
            $requestPath = $rewrite->getRequestPath();
        }

        if (!empty($requestPath)) {
            $routeParams['_direct'] = $requestPath;
        } else {
            $routePath         = 'review/product/view/id';
            $routeParams['id'] = $reviewId;
        }

        // reset cached URL instance GET query params
        if (!isset($routeParams['_query'])) {
            $routeParams['_query'] = array();
        }

        return $this->getUrlInstance()->setStore($storeId)
            ->getUrl($routePath, $routeParams);
    }
}
