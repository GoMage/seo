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
 * Helper for canonical url of CMS Page
 *
 * @category   GoMage
 * @package    GoMage_SeoBooster
 * @subpackage Helper
 * @author     Roman Bublik <rb@gomage.com>
 */
class GoMage_SeoBooster_Helper_Canonical_Cms extends GoMage_SeoBooster_Helper_Canonical_Abstract
{
    /**
     * Can use canonical tags
     *
     * @return bool
     */
    public function canUseCanonicalTag()
    {
        if (!$this->_moduleEnabled()) {
            return false;
        }

        return true;
    }

    /**
     * Return canonical url
     *
     * @param Mage_Cms_Model_Page $page Cms Page
     * @return string
     */
    public function getCanonicalUrl($page)
    {
        $params = array(
            '_direct' => $page->getIdentifier(),
            '_nosid' => true,
            '_store_to_url' => false,
            '_ignore_store' => true
        );

        $storeId = $this->getCanonicalStore($page);
        if ($storeId !== GoMage_SeoBooster_Helper_Data::CANONICAL_URL_DEFAULT_DOMAIN_VALUE) {
            $store = Mage::app()->getStore($storeId);
            if ($store->getIsActive()) {
                $params['_store'] = $store->getId();
            }
        }

        return Mage::helper('gomage_seobooster')->getUrl(null, $params);
    }

    /**
     * Return store for canonical url
     *
     * @param Mage_Cms_Model_Page $page Cms Page
     * @return mixed
     */
    public function getCanonicalStore($page)
    {
        $canonicalProductStore = $page->getCanonicalUrlStore();
        if ($canonicalProductStore != GoMage_SeoBooster_Helper_Data::CANONICAL_URL_DEFAULT_DOMAIN_VALUE) {
            return (int) $canonicalProductStore;
        }

        return Mage::app()->getStore()->getId();
    }
}
