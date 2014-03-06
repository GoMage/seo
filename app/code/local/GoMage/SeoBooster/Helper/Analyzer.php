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
 * Short description of the class
 *
 * Long description of the class (if any...)
 *
 * @category   GoMage
 * @package    GoMage_SeoBooster
 * @subpackage Helper
 * @author     Roman Bublik <rb@gomage.com>
 */
class GoMage_SeoBooster_Helper_Analyzer extends Mage_Core_Helper_Data
{
    const NAME_FIELD = 'name';
    const DESCRIPTION_FIELD = 'description';
    const META_TITLE_FIELD = 'meta_title';
    const META_DESCRIPTION_FIELD = 'meta_description';
    const META_KEYWORD_FIELD = 'meta_keyword';

    const PRODUCT_DUPLICATE_ACTION = 'productduplicate';
    const CATEGORY_DUPLICATE_ACTION = 'categoryduplicate';
    const PAGE_DUPLICATE_ACTION = 'pageduplicate';

    public function getNameMinCharsCount()
    {
        return Mage::getStoreConfig('gomage_seobooster/seo_analyzer/min_name_symbols');
    }

    public function getNameCharsCountLimit()
    {
        return Mage::getStoreConfig('gomage_seobooster/seo_analyzer/name_symbols_limit');
    }

    public function getDescriptionMinCharsCount()
    {
        return Mage::getStoreConfig('gomage_seobooster/seo_analyzer/min_desctiption_symbols');
    }

    public function getDescriptionCharsCountLimit()
    {
        return Mage::getStoreConfig('gomage_seobooster/seo_analyzer/description_limit');
    }

    public function getMetaTitleMinCharsCount()
    {
        return Mage::getStoreConfig('gomage_seobooster/seo_analyzer/min_meta_title_symbols');
    }

    public function getMetaTitleCharsCountLimit()
    {
        return Mage::getStoreConfig('gomage_seobooster/seo_analyzer/meta_title_limit');
    }

    public function getMetaDescriptionMinCharsCount()
    {
        return Mage::getStoreConfig('gomage_seobooster/seo_analyzer/min_meta_description_symbols');
    }

    public function getMetaDescriptionCharsCountLimit()
    {
        return Mage::getStoreConfig('gomage_seobooster/seo_analyzer/meta_description_limit');
    }

    public function getMetaKeywordMinCharsCount()
    {
        return Mage::getStoreConfig('gomage_seobooster/seo_analyzer/min_meta_keywords_qty');
    }

    public function getMetaKeywordCharsCountLimit()
    {
        return Mage::getStoreConfig('gomage_seobooster/seo_analyzer/keywords_qty_limit');
    }

    public function getMinCharsCountLimit($field)
    {
        switch ($field) {
            case self::NAME_FIELD:
                return $this->getNameMinCharsCount();
            case self::DESCRIPTION_FIELD:
                return $this->getDescriptionMinCharsCount();
            case self::META_TITLE_FIELD:
                return $this->getMetaTitleMinCharsCount();
            case self::META_DESCRIPTION_FIELD:
                return $this->getMetaDescriptionMinCharsCount();
            case self::META_KEYWORD_FIELD:
                return $this->getMetaKeywordMinCharsCount();
            default:
                return false;
        }
    }

    public function getCharsCountLimit($field)
    {
        switch ($field) {
            case self::NAME_FIELD:
                return $this->getNameCharsCountLimit();
            case self::DESCRIPTION_FIELD:
                return $this->getDescriptionCharsCountLimit();
            case self::META_TITLE_FIELD:
                return $this->getMetaTitleCharsCountLimit();
            case self::META_DESCRIPTION_FIELD:
                return $this->getMetaDescriptionCharsCountLimit();
            case self::META_KEYWORD_FIELD:
                return $this->getMetaKeywordCharsCountLimit();
            default:
                return false;
        }
    }

    public function getAnalyzeFields()
    {
        return array(
            self::NAME_FIELD,
            self::DESCRIPTION_FIELD,
            self::META_TITLE_FIELD,
            self::META_DESCRIPTION_FIELD,
            self::META_KEYWORD_FIELD
        );
    }

    public function getAnalyzeFieldLabels()
    {
        return array(
            self::NAME_FIELD => $this->__('Name'),
            self::DESCRIPTION_FIELD => $this->__('Description'),
            self::META_TITLE_FIELD => $this->__('Meta Title'),
            self::META_DESCRIPTION_FIELD => $this->__('Meta Description'),
            self::META_KEYWORD_FIELD => $this->__('Meta Keywords')
        );
    }

    public function getAnalyzeFieldLabel($field)
    {
        $labels = $this->getAnalyzeFieldLabels();
        if (isset($labels[$field])) {
            return $labels[$field];
        }

        return '';
    }
}
