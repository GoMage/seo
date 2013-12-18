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
 * Product model
 *
 * @category   GoMage
 * @package    GoMage_SeoBooster
 * @subpackage Model
 * @author     Roman Bublik <rb@gomage.com>
 */
class GoMage_SeoBooster_Model_Catalog_Product extends Mage_Catalog_Model_Product
{
    const CANONICAL_PRODUCT_URL_TYPE_CONFIG_VALUE = 0;
    /**
     * Return product category with high Level
     *
     * @return Mage_Catalog_Model_Category
     */
    public function getHighLevelCategory()
    {
        if (!$this->hasData('high_level_category')) {
            $categories = $this->getCategoryCollection()
                ->setStoreId($this->getStoreId())
                ->addAttributeToSelect('is_active')
                ->setOrder('level')
                ->setOrder('entity_id');
            $category = $categories->getFirstItem();
            $this->setData('high_level_category', $category);
        }

        return $this->getData('high_level_category');
    }
}
