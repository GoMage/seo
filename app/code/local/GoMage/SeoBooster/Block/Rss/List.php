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

class GoMage_SeoBooster_Block_Rss_List extends Mage_Rss_Block_List
{
    public function CategoriesRssFeed()
    {
        $path = self::XML_PATH_RSS_METHODS.'/catalog/category';
        if((bool)Mage::getStoreConfig($path)){
            $category = Mage::getModel('catalog/category');

            /* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection */
            $treeModel = $category->getTreeModel()->loadNode(Mage::app()->getStore()->getRootCategoryId());
            $nodes = $treeModel->loadChildren()->getChildren();

            $nodeIds = array();
            foreach ($nodes as $node) {
                $nodeIds[] = $node->getId();
            }

            $collection = $category->getCollection()
                ->addAttributeToSelect('url_key')
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('is_anchor')
                ->addAttributeToFilter('is_active',1)
                ->addIdFilter($nodeIds)
                ->addAttributeToSort('name')
                ->load();

            foreach ($collection as $category) {
                if (Mage::helper('gomage_seobooster')->canUseRssUrlRewrite()) {
                    $this->addRssUrlRewriteFeed($category);
                } else {
                    $this->addRssFeed('rss/catalog/category', $category->getName(),array('cid'=>$category->getId()));
                }
            }
        }
    }

    /**
     * Add new rss feed
     *
     * @param string $category Category
     * @return $this
     */
    public function addRssUrlRewriteFeed($category)
    {
        $this->_rssFeeds[] = new Varien_Object(
            array(
                'url'   => Mage::getModel('gomage_seobooster/rss_url')->getUrl($category->getId(), $this->getCurrentStoreId()),
                'label' => $category->getName()
            )
        );

        return $this;
    }
}
