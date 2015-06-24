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
class GoMage_SeoBooster_Model_Catalog_Layer_Filter_Attribute extends Mage_Catalog_Model_Layer_Filter_Attribute
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

        $key  = $this->getLayer()->getStateKey() . '_' . $this->_requestVar;
        $data = $this->getLayer()->getAggregator()->getCacheData($key);

        if ($data === null) {
            $options      = $attribute->getFrontend()->getSelectOptions();
            $optionsCount = $this->_getResource()->getCount($this);
            $data         = array();
            foreach ($options as $option) {
                if (is_array($option['value'])) {
                    continue;
                }
                if (Mage::helper('core/string')->strlen($option['value'])) {
                    $value = Mage::helper('gomage_seobooster/layered')->canUseFriendlyUrl() ?
                        Mage::helper('gomage_seobooster')->formatUrlValue($option['label'], $option['value']) : $option['value'];
                    // Check filter type
                    if ($this->_getIsFilterableAttribute($attribute) == self::OPTIONS_ONLY_WITH_RESULTS) {
                        if (!empty($optionsCount[$option['value']])) {
                            $data[] = array(
                                'label' => $option['label'],
                                'value' => $value,
                                'count' => $optionsCount[$option['value']],
                            );
                        }
                    } else {
                        $data[] = array(
                            'label' => $option['label'],
                            'value' => $value,
                            'count' => isset($optionsCount[$option['value']]) ? $optionsCount[$option['value']] : 0,
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

    /**
     * Apply attribute option filter to product collection
     *
     * @param   Zend_Controller_Request_Abstract $request Request
     * @param   Varien_Object $filterBlock Filter Block
     * @return  Mage_Catalog_Model_Layer_Filter_Attribute
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        if (!Mage::helper('gomage_seobooster/layered')->canUseFriendlyUrl()) {
            return parent::apply($request, $filterBlock);
        }


        $filter = $request->getParam($this->_requestVar);
        if (is_array($filter)) {
            return $this;
        }
        $optionId = $this->_getOptionId($filter);

        if ($optionId) {
            $this->_getResource()->applyFilterToCollection($this, $optionId);
            $this->getLayer()->getState()->addFilter($this->_createItem($this->_getOptionText($optionId), $optionId));
            $this->_items = array();
        }
        return $this;
    }
}
