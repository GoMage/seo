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
 * Opengraph protocol for product
 *
 * @category   GoMage
 * @package    GoMage_SeoBooster
 * @subpackage Helper
 * @author     Roman Bublik <rb@gomage.com>
 */
class GoMage_SeoBooster_Helper_Opengraph_Product extends GoMage_SeoBooster_Helper_Opengraph_Abstract
{
    /**
     * Return product entity
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getEntity()
    {
        return Mage::registry('product');
    }

    /**
     * Return product canonical url
     *
     * @return string
     */
    public function getCanonicalUrl()
    {
        $entity = $this->getEntity();
        if ($entity && $entity->getId()) {
            return Mage::helper('gomage_seobooster/product')->getCanonicalUrl($entity);
        }

        return '';
    }

    /**
     * Return product image url
     *
     * @return string
     */
    public function getImage()
    {
        $entity = $this->getEntity();

        if ($entity && $entity->getImage()) {
            return Mage::helper('catalog/image')->init($entity, 'image')->__toString();
        }

        return '';
    }
}
