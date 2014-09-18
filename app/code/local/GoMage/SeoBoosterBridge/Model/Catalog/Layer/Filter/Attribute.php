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
            $selected = explode(',', $value);
            if (Mage::helper('gomage_seobooster/layered')->canUseFriendlyUrl()) {
                foreach ($selected as $_k => $_v) {
                    $selected[$_k] = $this->_getOptionId($_v);
                }
            }
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
                        $value  = Mage::helper('gomage_seobooster/layered')->canUseFriendlyUrl() ?
                            Mage::helper('gomage_seobooster')->formatUrlValue($option['label'], $option['value']) : $option['value'];
                    } else {
                        $active = false;
                        if (!empty($selected) && $attribute->getFilterType() != GoMage_Navigation_Model_Layer::FILTER_TYPE_DROPDOWN) {
                            $value = array_merge($selected, (array)$option['value']);
                            if (Mage::helper('gomage_seobooster/layered')->canUseFriendlyUrl()) {
                                foreach ($value as $_k => $_v) {
                                    $value[$_k] = Mage::helper('gomage_seobooster')->formatUrlValue($this->_getOptionText($_v));
                                }
                            }
                            $value = implode(',', $value);
                        } else {
                            $value = Mage::helper('gomage_seobooster/layered')->canUseFriendlyUrl() ?
                                Mage::helper('gomage_seobooster')->formatUrlValue($option['label'], $option['value']) : $option['value'];
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
        if (Mage::helper('gomage_seobooster/layered')->canUseFriendlyUrl()) {
            $options = $this->getAttributeModel()->getSource()->getAllOptions();
            foreach ($options as $option) {
                if (strcasecmp($option['label'], $value) == 0 || $option['value'] == $value || $value == Mage::helper('gomage_seobooster')->formatUrlValue($option['label'])) {
                    return $option['value'];
                }
            }
            return null;
        }
        return $this->getAttributeModel()->getSource()->getOptionId($value);
    }

    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        $filter = $request->getParam($this->_requestVar);

        if (is_array($filter)) {
            return $this;
        }

        if ($filter) {
            $filters = explode(',', $filter);
            if (Mage::helper('gomage_seobooster/layered')->canUseFriendlyUrl()) {
                foreach ($filters as $_k => $_v) {
                    $filters[$_k] = $this->_getOptionId($_v);
                }
            }
            $this->_getResource()->applyFilterToCollection($this, $filters);
            foreach ($filters as $filter) {
                if (Mage::helper('gomage_seobooster/layered')->canUseFriendlyUrl()) {
                    $filter = $this->_getOptionId($filter);
                }
                $text = $this->_getOptionText($filter);
                $this->getLayer()->getState()->addFilter($this->_createItem($text, $filter));
            }

        }
        return $this;
    }

    public function getResetValue($value_to_remove = null)
    {
        if ($value_to_remove && ($current_value = Mage::helper('gomage_navigation')->getRequest()->getParam($this->_requestVar))) {
            $current_value = explode(',', $current_value);
            if (Mage::helper('gomage_seobooster/layered')->canUseFriendlyUrl()) {
                foreach ($current_value as $_k => $_v) {
                    $current_value[$_k] = $this->_getOptionId($_v);
                }
                $value_to_remove = $this->_getOptionId($value_to_remove);
            }
            if (false !== ($position = array_search($value_to_remove, $current_value))) {
                unset($current_value[$position]);
            }
            if (!empty($current_value)) {
                if (Mage::helper('gomage_seobooster/layered')->canUseFriendlyUrl()) {
                    foreach ($current_value as $_k => $_v) {
                        $current_value[$_k] = Mage::helper('gomage_seobooster')->formatUrlValue($this->_getOptionText($_v));
                    }
                }
                return implode(',', $current_value);
            }
        }

        return null;
    }

}
