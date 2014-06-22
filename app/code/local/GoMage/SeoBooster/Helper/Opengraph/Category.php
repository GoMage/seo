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
        if ($entity && $entity->getId() && $entity->getImage()) {
            return $this->_getImageInfo($entity->getImage());
        }

        return '';
    }

    protected function _getImageInfo($_image)
    {
        $imageFile = is_string($_image) ? $_image : $_image->getFile();
        $baseDir   = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'category';

        $imagePath = $baseDir . DS . $imageFile;
        $processor = new Varien_Image($imagePath);

        $image_url        = Mage::getBaseUrl('media') . 'catalog/category/' . $imageFile;
        $image_url_secure = Mage::getBaseUrl('media', true) . 'catalog/category/' . $imageFile;

        return array(
            'image'        => $image_url,
            'image_secure' => $image_url_secure,
            'width'        => $processor->getOriginalWidth(),
            'height'       => $processor->getOriginalHeight(),
            'type'         => mime_content_type($imagePath)
        );
    }

}
