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
 * Review helper
 *
 * @category   GoMage
 * @package    GoMage_SeoBooster
 * @subpackage Block
 * @author     Roman Bublik <rb@gomage.com>
 */
class GoMage_SeoBooster_Block_Review_Helper extends Mage_Review_Block_Helper
{
    /**
     * Return reviews url
     *
     * @return string
     */
    public function getReviewsUrl()
    {
        if (Mage::helper('gomage_seobooster')->canUseProductReviewsUrlRewrite()) {
            $product = $this->getProduct();
            return $product->getUrlModel()->getProductReviewsUrl($product);
        }

        return parent::getReviewsUrl();
    }
}