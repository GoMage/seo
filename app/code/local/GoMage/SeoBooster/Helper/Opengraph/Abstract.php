<?php

/**
 * GoMage Seo Booster Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2013-2015 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use/
 * @version      Release: 1.2.0
 * @since        Available since Release 1.0.0
 */
abstract class GoMage_SeoBooster_Helper_Opengraph_Abstract extends Mage_Core_Helper_Data
{
    /**
     * Return entity
     *
     * @return mixed
     */
    abstract public function getEntity();

    /**
     * Return canonical url
     *
     * @return string
     */
    abstract public function getCanonicalUrl();

    /**
     * Return entity image url
     *
     * @return string|array
     */
    abstract public function getImage();

    /**
     * Can add opengraph metadata
     *
     * return bool
     */
    public function canAddOpengraphMetaData()
    {
        return Mage::helper('gomage_seobooster')->isEnabled() &&
        Mage::getStoreConfig('gomage_seobooster/general/enable_opengraph_metadata');
    }

    /**
     * Add metadata to head block
     *
     * @return mixed
     */
    public function addMetadata()
    {
        $entity = $this->getEntity();
        if ($headBlock = $this->getHeadBlock()) {
            $ogMetaBlock = $headBlock->getChild('head.og.metadata');
        }

        if ($entity && $entity->getId() && isset($ogMetaBlock)) {
            if ($title = $entity->getName()) {
                $ogMetaBlock->addItem('og:title', htmlspecialchars(strip_tags($title)));
            }
            if ($description = $entity->getDescription()) {
                $ogMetaBlock->addItem('og:description', htmlspecialchars(strip_tags($description)));
            }
            if ($url = $this->getCanonicalUrl()) {
                $ogMetaBlock->addItem('og:url', $url);
            }

            if ($storeName = $this->_getStoreName()) {
                $ogMetaBlock->addItem('og:site_name', addslashes($storeName));
            }

            $ogMetaBlock->addItem('og:type', 'website');
            $ogMetaBlock->addItem('og:locale', $this->getStoreLocale());

            $this->_addLacales($ogMetaBlock);
            $this->_addImages($ogMetaBlock);
        }

        return $this;
    }

    /**
     * Add Store locales
     *
     * @param $ogMetaBlock
     */
    protected function _addLacales($ogMetaBlock)
    {
        $locales = $this->getStoresLocale();
        foreach ($locales as $locale) {
            $ogMetaBlock->addItem('og:locale:alternate', $locale);
        }
    }

    /**
     * Add entity images
     *
     * @param $ogMetaBlock
     */
    protected function _addImages($ogMetaBlock)
    {
        if ($image = $this->getImage()) {
            if (is_array($image)) {
                $ogMetaBlock->addItem('og:image', $image['image']);
                $ogMetaBlock->addItem('og:image:url', $image['image']);
                if (isset($image['image_secure']) && $image['image'] != $image['image_secure']) {
                    $ogMetaBlock->addItem('og:image:secure_url', $image['image_secure']);
                }
                $ogMetaBlock->addItem('og:image:type', $image['type']);
                $ogMetaBlock->addItem('og:image:width', $image['width']);
                $ogMetaBlock->addItem('og:image:height', $image['height']);
            } else {
                if (is_string($image)) {
                    $ogMetaBlock->addItem('og:image', $image);
                    $ogMetaBlock->addItem('og:image:url', $image);
                }
            }
        }
    }

    /**
     * Return head block
     *
     * @return Mage_Page_Block_Html_Head
     */
    public function getHeadBlock()
    {
        return Mage::app()->getLayout()->getBlock('head');
    }

    protected function _getStoreName()
    {
        return Mage::getStoreConfig('general/store_information/name');
    }

    public function getStoreLocale($storeId = null)
    {
        if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }

        return Mage::getStoreConfig('general/locale/code', $storeId);
    }

    public function getStoresLocale()
    {
        $locales        = array();
        $currentStoreId = Mage::app()->getStore()->getId();
        $stores         = Mage::app()->getStores();
        foreach ($stores as $store) {
            if ($store->getId() != $currentStoreId) {
                $locale           = $this->getStoreLocale($store->getId());
                $locales[$locale] = $locale;
            }
        }

        return $locales;
    }

}
