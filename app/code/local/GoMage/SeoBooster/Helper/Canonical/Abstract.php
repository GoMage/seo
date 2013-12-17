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
abstract class GoMage_SeoBooster_Helper_Canonical_Abstract extends Mage_Core_Helper_Data
{
    abstract public function canUseCanonicalTag();

    abstract public function getCanonicalUrl($object);

    abstract public function getCanonicalStore($object);

    protected function _moduleEnabled()
    {
        return Mage::helper('gomage_seobooster')->isEnabled();
    }
}
