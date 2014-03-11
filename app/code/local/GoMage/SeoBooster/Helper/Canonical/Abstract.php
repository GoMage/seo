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
 * Abstract helper class for canonical urls
 *
 * @category   GoMage
 * @package    GoMage_SeoBooster
 * @subpackage Helper
 * @author     Roman Bublik <rb@gomage.com>
 */
abstract class GoMage_SeoBooster_Helper_Canonical_Abstract extends Mage_Core_Helper_Data
{
    /**
     * Can use canonical tags
     *
     * @return bool
     */
    abstract public function canUseCanonicalUrl();

    /**
     * Return canonical url
     *
     * @param Object $object Object
     * @return string
     */
    abstract public function getCanonicalUrl($object);

    /**
     * Return store for canonical url
     *
     * @param Object $object Object
     * @return mixed
     */
    abstract public function getCanonicalStore($object);

    /**
     * Return module status
     *
     * @return bool
     */
    protected function _moduleEnabled()
    {
        return Mage::helper('gomage_seobooster')->isEnabled();
    }
}
