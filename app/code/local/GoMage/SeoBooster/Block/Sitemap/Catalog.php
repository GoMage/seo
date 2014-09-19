<?php 
/**
 * GoMage Seo Booster Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2013-2014 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use/
 * @version      Release: 1.1.0
 * @since        Available since Release 1.0.0
 */

class GoMage_SeoBooster_Block_Sitemap_Catalog extends Mage_Catalog_Block_Seo_Sitemap_Tree_Category
{
    /**
     * Initialize categories collection
     *
     * @return Mage_Catalog_Block_Seo_Sitemap_Category
     */
    protected function _prepareLayout()
    {
        $helper = Mage::helper('catalog/category');
        /* @var $helper Mage_Catalog_Helper_Category */
        $parent = Mage::getModel('catalog/category')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load(Mage::app()->getStore()->getRootCategoryId());
        $this->_storeRootCategoryPath = $parent->getPath();
        $this->_storeRootCategoryLevel = $parent->getLevel();
        $collection = $this->getTreeCollection()
            ->addPathsFilter($this->_storeRootCategoryPath . '/');
        $this->_prepareCategoryProducts($collection);
        $this->setCollection($collection);
        return $this;
    }

    /**
     * Prepare products for categories
     *
     * @param Mage_Catalog_Model_Resource_Category_Collection $categories Categories
     * @return $this
     */
    protected function _prepareCategoryProducts($categories)
    {
        $products = Mage::getResourceModel('catalog/product_collection')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->addAttributeToSelect('name')
            ->addCategoryIds()
            ->addUrlRewrite();
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);

        foreach ($products as $product) {
            $categoryIds = $product->getCategoryIds();
            foreach ($categoryIds as $categoryId) {
                if ($category = $categories->getItemById($categoryId)) {
                    if ($categoryProducts = $category->getProducts()) {
                        $categoryProducts[] = $product;
                    } else {
                        $categoryProducts = array($product);
                    }
                    $category->setProducts($categoryProducts);
                }

            }
        }

        return $this;
    }

    /**
     * Retrieve Product URL using UrlDataObject
     *
     * @param Mage_Catalog_Model_Product $product    Product
     * @param array                      $additional The route params
     * @return string
     */
    public function getProductUrl($product, $additional = array())
    {
        if (!isset($additional['_escape'])) {
            $additional['_escape'] = true;
        }

        return $product->getUrlModel()->getUrl($product, $additional);
    }
}
