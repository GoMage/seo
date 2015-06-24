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
class GoMage_SeoBooster_Block_Catalog_Product_RichSnippet extends Mage_Catalog_Block_Product_View
{
    /**
     * Return product image url
     *
     * @return string
     */
    public function getImage()
    {
        $product = $this->getProduct();
        return Mage::helper('catalog/image')->init($product, 'image')->__toString();
    }

    /**
     * Return product description
     *
     * @return string
     */
    public function getDescription()
    {
        return strip_tags($this->getProduct()->getDescription());
    }

    /**
     * Return categories string
     *
     * @return string
     */
    public function getCategory()
    {
        $categories = $this->getCategories();
        if (!$categories) {
            return false;
        }

        $names = array();
        foreach ($categories as $category) {
            $names[] = $category->getName();
        }

        return implode(' > ', $names);
    }


    /**
     * Return categories array
     *
     * @return array
     */
    public function getCategories()
    {
        $product = $this->getProduct();
        if (!$product->getCategoryId()) {
            return false;
        }
        return $product->getCategory()->getParentCategories();
    }

    /**
     * Return currency code
     *
     * @return string
     */
    public function getCurrencyCode()
    {
        Mage::getUrl('*/*/*');
        return Mage::app()->getStore()->getCurrentCurrencyCode();
    }

    /**
     * Return offer url
     *
     * @return mixed
     */
    public function getOfferUrl()
    {
        return Mage::helper('gomage_seobooster')->getUrl('*/*/*', array(
                '_current'     => true,
                '_use_rewrite' => true,
            )
        );
    }

    /**
     * Return review rating
     *
     * @return float
     */
    public function getRatingSummary()
    {
        return $this->getProduct()->getRatingSummary()->getRatingSummary() / 20;
    }

    /**
     * Return reviews count
     *
     * @return int
     */
    public function getReviewsCount()
    {
        return $this->getProduct()->getRatingSummary()->getReviewsCount();
    }

    /**
     * Return product stock status
     *
     * @return string
     */
    public function getStockStatus()
    {
        return $this->getProduct()->getIsSalable() ? 'in_stock' : 'out_of_stock';
    }

    /**
     * Return product qty
     *
     * @return float|bool
     */
    public function getQty()
    {
        if (!$this->getProduct()->getIsSalable()) {
            return false;
        }

        if (($this->getProduct()->getStockItem()->getManageStock()) &&
            ($this->getProduct()->getStockItem()->getQty() > 0)
        ) {
            return $this->getProduct()->getStockItem()->getQty();
        }

        return false;
    }

    public function isBreadcrumbs()
    {
        return Mage::getStoreConfig('gomage_seobooster/general/rich_snippets_link') == GoMage_SeoBooster_Model_Config_Source_Richsnippetslink::BREADCRUMBS;
    }

    protected function _toHtml()
    {
        if (Mage::helper('gomage_seobooster')->isRichSnippetEnabled()) {
            return parent::_toHtml();
        }

        return '';
    }
}
