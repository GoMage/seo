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
 * Rich snippet block
 *
 * @category   GoMage
 * @package    GoMage_SeoBooster
 * @subpackage Block
 * @author     Roman Bublik <rb@gomage.com>
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
        $categories = array();
        $product = $this->getProduct();
        if (!$product->getCategoryId()) {
            return false;
        }

        $categoryCollection = $product->getCategory()->getParentCategories();
        foreach ($categoryCollection as $_category) {
            $categories[] = $_category->getName();
        }

        return implode(' > ', $categories);
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
            '_current'=>true,
            '_use_rewrite'=>true,
        ));
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

    protected function _toHtml()
    {
        if (Mage::helper('gomage_seobooster')->isRichSnippetEnabled()) {
            return parent::_toHtml();
        }

        return '';
    }
}
