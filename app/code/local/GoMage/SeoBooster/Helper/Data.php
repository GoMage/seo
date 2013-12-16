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

    public function isEnabled()
    {
        return Mage::getStoreConfig('gomage_seobooster/general/enabled');
    }
    public function ga()
    {
        return Zend_Json::decode(base64_decode(Mage::helper('core')->decrypt(Mage::getStoreConfig('gomage_activation/designer/ar'))));
    }
}
