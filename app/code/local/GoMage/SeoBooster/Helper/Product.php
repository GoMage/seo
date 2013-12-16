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
 * Short description of the class
 *
 * Long description of the class (if any...)
 *
 * @category   GoMage
 * @package    GoMage_SeoBooster
 * @subpackage Helper
 * @author     Roman Bublik <rb@gomage.com>
 */
class GoMage_SeoBooster_Helper_Product extends GoMage_SeoBooster_Helper_Canonical_Abstract
{
    const CANONICAL_PRODUCT_URL_LONGEST = 1;
    const CANONICAL_PRODUCT_URL_SHORTEST = 2;

    public function canUseCanonicalTag()
    {
        if (!$this->_moduleEnabled()) {
            return false;
        }

        $canonicalUrlEnabled = Mage::getStoreConfig('gomage_seobooster/general/enable_canonical_url');
        return ($canonicalUrlEnabled == GoMage_SeoBooster_Helper_Data::CANONICAL_URL_PRODUCTS)
            || ($canonicalUrlEnabled == GoMage_SeoBooster_Helper_Data::CANONICAL_URL_PRODUCTS_CATEGORIES);
    }

    public function getCanonicalUrl($product)
    {
        if ($this->canUseCanonicalTag()) {
            $params = array();
            if ($this->getCanonicalUrlType($product) == self::CANONICAL_PRODUCT_URL_SHORTEST) {
                $params['_ignore_category'] = true;
            } else {
                $highLevelCategory = $product->getHighLevelCategory();
                $params['category'] = $highLevelCategory && $highLevelCategory->getId() ? $highLevelCategory->getId()
                    : $product->getCategoryId();
            }
            return $product->getUrlModel()->getUrl($product, $params);
        }

        return false;
    }

    public function getCanonicalUrlType($product)
    {
        return Mage::getStoreConfig('gomage_seobooster/general/product_canonical_url');
    }
}
