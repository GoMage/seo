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

class GoMage_SeoBooster_Block_Adminhtml_Analyzer_Grid_Renderer_Options
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Options
{
    /**
     * Render a grid cell as options
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $options = $this->getColumn()->getOptions();
        $fieldsMap = GoMage_SeoBooster_Model_Resource_Analyzer_Product_Collection::getFieldsMap();
        if (!empty($options) && is_array($options)) {
            $value = $row->getData($this->getColumn()->getIndex());
            if (is_array($value)) {
                $res = array();
                foreach ($value as $item) {
                    if (isset($options[$item])) {
                        $label = $options[$item];
                        if ($item === GoMage_SeoBooster_Model_Analyzer::LONG_ERROR ||
                            $item === GoMage_SeoBooster_Model_Analyzer::SHORT_ERROR) {
                            $countField = $fieldsMap[$this->getColumn()->getIndex()];
                            $label .= " ({$row->getData($countField)})";
                            $label = $this->escapeHtml($label);
                        } elseif ($item == GoMage_SeoBooster_Model_Analyzer::DUPLICATE_ERROR) {
                            if ($duplicates = $row->getData('duplicate_'. $this->getColumn()->getIndex())) {
                                $duplicatesCount = count($duplicates);
                                $duplicateEntityId = $row->getData('duplicate_entity_id');
                                $label = $this->escapeHtml($label);
                                $label .= " (<a href='". $this->_getDuplicateUrl($this->getColumn()->getIndex(), $duplicateEntityId) ."' target='_blank'>{$duplicatesCount}</a>)";
                            }
                        }
                        $res[] = $label;
                    }
                }
                return implode("<br/>", $res);
            } elseif (isset($options[$value])) {
                if ($value === GoMage_SeoBooster_Model_Analyzer::RESULT_OK) {
                    $label = $this->escapeHtml($options[$value]);
                    $label = "<span style='color:#629632; font-weight: bold'>". $label ."</span>";
                    return $label;
                }
                return $this->escapeHtml($options[$value]);
            } elseif (in_array($value, $options)) {
                return $this->escapeHtml($value);
            }
        }
    }

    protected function _getDuplicateUrl($field, $duplicateEntityId)
    {
        $action = $this->getColumn()->getData('duplicate_action');
        return Mage::helper('adminhtml')->getUrl('*/*/'. $action, array('duplicate_entity_id' => $duplicateEntityId, 'duplicate_field' => $field));
    }
}
