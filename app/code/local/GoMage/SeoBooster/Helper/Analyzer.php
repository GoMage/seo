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
class GoMage_SeoBooster_Helper_Analyzer extends Mage_Core_Helper_Data
{
    const NAME_FIELD = 'name';

    public function getNameMinCharsCount()
    {
        return Mage::getStoreConfig('gomage_seobooster/seo_analayzer/min_name_symbols');
    }

    public function getNameCharsCountLimit()
    {
        return Mage::getStoreConfig('gomage_seobooster/seo_analayzer/name_symbols_limit');
    }

    public function getMinCharsCountLimit($field)
    {
        switch ($field) {
            case self::NAME_FIELD:
                return $this->getNameMinCharsCount();
            default:
                return false;
        }
    }
}
