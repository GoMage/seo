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
 * Opengraph abstract class
 *
 * @category   GoMage
 * @package    GoMage_SeoBooster
 * @subpackage Helper
 * @author     Roman Bublik <rb@gomage.com>
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
     * @return string
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
                $ogMetaBlock->addItem('og:title', strip_tags($title));
            }
            if ($description = $entity->getDescription()) {
                $ogMetaBlock->addItem('og:description', strip_tags($description));
            }
            if ($url = $this->getCanonicalUrl()) {
                $ogMetaBlock->addItem('og:url', $url);
            }
            if ($image = $this->getImage()) {
                $ogMetaBlock->addItem('og:image', $image);
            }
            $ogMetaBlock->addItem('og:type', 'website');
        }

        return $this;
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
}
