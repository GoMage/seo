<?php

/**
 * GoMage Seo Booster Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2013-2014 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use/
 * @version      Release: 1.0.0
 * @since        Available since Release 1.0.0
 */
class GoMage_SeoBoosterBridge_Model_Catalog_Layer_Filter_Attribute extends GoMage_Navigation_Model_Layer_Filter_Attribute
{
    /**
     * Get data array for building attribute filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        $attribute         = $this->getAttributeModel();
        $this->_requestVar = $attribute->getAttributeCode();

        $option_images = $attribute->getOptionImages();

        $selected = array();

        if ($value = Mage::helper('gomage_navigation')->getRequest()->getParam($this->_requestVar)) {
            $selected = array_merge($selected, explode(',', $value));
        }

        $key  = $this->getLayer()->getStateKey() . '_' . $this->_requestVar;
        $data = $this->getLayer()->getAggregator()->getCacheData($key);

        if ($data === null) {

            $filter_mode = Mage::helper('gomage_navigation')->isGomageNavigation();
            $options     = $attribute->getFrontend()->getSelectOptions();

            if (Mage::helper('gomage_navigation')->isEnterprise()) {
                $isCatalog = is_null(Mage::helper('gomage_navigation')->getParam('q'));

                $helper = Mage::helper('enterprise_search');
                if (!$isCatalog && $helper->isThirdPartSearchEngine() && $helper->getIsEngineAvailableForNavigation($isCatalog) && Mage::helper('gomage_navigation')->isGomageNavigation()) {
                    $engine    = Mage::getResourceSingleton('enterprise_search/engine');
                    $fieldName = $engine->getSearchEngineFieldName($attribute, 'nav');

                    $productCollection = $this->getLayer()->getProductCollection();
                    $optionsCount      = $productCollection->getFacetedData($fieldName);

                } else {
                    $optionsCount = $this->_getResource()->getCount($this);
                }
            } else {
                $optionsCount = $this->_getResource()->getCount($this);
            }

            $data = array();

            foreach ($options as $option) {

                $image = '';

                if (is_array($option['value']) || (in_array($option['value'], $selected) && !$filter_mode)) {
                    continue;
                }

                if (Mage::helper('core/string')->strlen($option['value'])) {

                    if ($option_images && isset($option_images[$option['value']])) {
                        $image = $option_images[$option['value']];
                    }

                    if (in_array($option['value'], $selected) && $filter_mode) {
                        $active = true;
                        $value  = Mage::helper('gomage_seobooster/layered')->canUseFriendlyUrl() ? strtolower($option['label']) : $option['value'];
                    } else {
                        $active = false;
                        if (!empty($selected) && $attribute->getFilterType() != GoMage_Navigation_Model_Layer::FILTER_TYPE_DROPDOWN) {
                            $value = implode(',', array_merge($selected, (array)$option['value']));
                        } else {
                            $value = Mage::helper('gomage_seobooster/layered')->canUseFriendlyUrl() ? strtolower($option['label']) : $option['value'];
                        }
                    }

                    // Check filter type
                    if ($this->_getIsFilterableAttribute($attribute) == self::OPTIONS_ONLY_WITH_RESULTS) {
                        if (!empty($optionsCount[$option['value']]) || in_array($option['value'], $selected)) {
                            $data[] = array(
                                'label'  => $option['label'],
                                'value'  => $value,
                                'count'  => isset($optionsCount[$option['value']]) ? $optionsCount[$option['value']] : 0,
                                'active' => $active,
                                'image'  => $image,
                            );
                        }
                    } else {
                        $data[] = array(
                            'label'  => $option['label'],
                            'value'  => $value,
                            'count'  => isset($optionsCount[$option['value']]) ? $optionsCount[$option['value']] : 0,
                            'active' => $active,
                            'image'  => $image,
                        );
                    }
                }
            }

            $tags = array(
                Mage_Eav_Model_Entity_Attribute::CACHE_TAG . ':' . $attribute->getId()
            );

            $tags = $this->getLayer()->getStateTags($tags);
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }

        return $data;
    }


    /**
     * Return option id by value
     *
     * @param string $value Value
     * @return int
     */
    protected function _getOptionId($value)
    {
        return $this->getAttributeModel()->getSource()->getOptionId($value);
    }

}
