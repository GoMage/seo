<?php
/**
 * GoMage Seo Booster Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2013-2015 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use/
 * @version      Release: 1.3.0
 * @since        Available since Release 1.0.0
 */

class GoMage_SeoBooster_Block_Catalog_Category_View extends Mage_Catalog_Block_Category_View
{
    /**
     * Prepare layout
     * Set category meta data to head block
     *
     * @return GoMage_SeoBooster_Block_Catalog_Category_View
     */
    protected function _prepareLayout()
    {
        Mage_Core_Block_Template::_prepareLayout();

        $this->getLayout()->createBlock('catalog/breadcrumbs');

        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $category = $this->getCurrentCategory();
            if ($title = $category->getMetaTitle()) {
                $headBlock->setTitle($title);
            }
            if ($description = $category->getMetaDescription()) {
                $headBlock->setDescription($description);
            }
            if ($keywords = $category->getMetaKeywords()) {
                $headBlock->setKeywords($keywords);
            }
            if (Mage::helper('gomage_seobooster/category')->canUseCanonicalUrl()) {
                if ($url = Mage::helper('gomage_seobooster/category')->getCanonicalUrl($category)){
                    $headBlock->addLinkRel('canonical', $url);
                }
            } elseif ($this->helper('catalog/category')->canUseCanonicalTag()) {
                $headBlock->addLinkRel('canonical', $category->getUrl());
            }
            if (Mage::helper('gomage_seobooster/category')->canAddNextPrevLinkRel()) {
                Mage::helper('gomage_seobooster/category')->addNextPrevLinkRel();
            }
            if (Mage::helper('gomage_seobooster/opengraph_category')->canAddOpengraphMetaData()) {
                Mage::helper('gomage_seobooster/opengraph_category')->addMetadata();
            }
            $headBlock->setRobots(Mage::helper('gomage_seobooster')->getRobots($category));

            /*
            want to show rss feed in the url
            */
            if ($this->IsRssCatalogEnable() && $this->IsTopCategory()) {
                $title = $this->helper('rss')->__('%s RSS Feed',$this->getCurrentCategory()->getName());
                $headBlock->addItem('rss', $this->getRssLink(), 'title="'.$title.'"');
            }
        }

        return $this;
    }

    /**
     * Return rss link
     *
     * @return string
     */
    public function getRssLink()
    {
        if (Mage::helper('gomage_seobooster')->canUseRssUrlRewrite()) {
            return Mage::getModel('gomage_seobooster/rss_url')
                ->getUrl($this->getCurrentCategory()->getId(), Mage::app()->getStore()->getId());
        }

        return parent::getRssLink();
    }
}
