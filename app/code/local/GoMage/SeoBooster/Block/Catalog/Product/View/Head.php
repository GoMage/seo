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

class GoMage_SeoBooster_Block_Catalog_Product_View_Head extends Mage_Core_Block_Template
{
    protected function _prepareLayout()
    {
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $product = $this->getProduct();
            if (Mage::helper('gomage_seobooster/product')->canUseCanonicalUrl()) {
                if ($url = Mage::helper('gomage_seobooster/product')->getCanonicalUrl($product)) {
                    $headBlock->addLinkRel('canonical', $url);
                }
            } elseif ($this->helper('catalog/product')->canUseCanonicalTag()) {
                $params = array('_ignore_category'=>true);
                $headBlock->addLinkRel('canonical', $product->getUrlModel()->getUrl($product, $params));
            }
            if (Mage::helper('gomage_seobooster/opengraph_product')->canAddOpengraphMetaData()) {
                Mage::helper('gomage_seobooster/opengraph_product')->addMetadata();
            }
            $headBlock->setRobots(Mage::helper('gomage_seobooster')->getRobots($product));
        }
    }

    /**
     * Retrieve current product model
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!Mage::registry('product') && $this->getProductId()) {
            $product = Mage::getModel('catalog/product')->load($this->getProductId());
            Mage::register('product', $product);
        }
        return Mage::registry('product');
    }
}
