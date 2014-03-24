<?php
/**
 * GoMage Seo Booster Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2013-2014 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use/
 * @version      Release: 1.0.0
 * @since        Available since Release 1.0.0
 */

class GoMage_SeoBooster_Helper_Opengraph_Category extends GoMage_SeoBooster_Helper_Opengraph_Abstract
{
    /**
     * Return product entity
     *
     * @return Mage_Catalog_Model_Category
     */
    public function getEntity()
    {
        return Mage::registry('current_category');
    }

    /**
     * Return category canonical url
     *
     * @return string
     */
    public function getCanonicalUrl()
    {
        $entity = $this->getEntity();
        if ($entity && $entity->getId()) {
            return Mage::helper('gomage_seobooster/category')->getCanonicalUrl($entity);
        }

        return '';
    }

    /**
     * Return category image url
     *
     * @return string
     */
    public function getImage()
    {
        $entity = $this->getEntity();
        if ($entity && $entity->getId()) {
            return $entity->getImageUrl();
        }

        return '';
    }
}
