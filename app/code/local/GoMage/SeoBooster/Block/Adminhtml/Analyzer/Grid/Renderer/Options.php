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
 * @subpackage Block
 * @author     Roman Bublik <rb@gomage.com>
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
//        Zend_Debug::dump($row->getData());die;
        $showMissingOptionValues = (bool)$this->getColumn()->getShowMissingOptionValues();
        $fieldsMap = GoMage_SeoBooster_Model_Resource_Analayzer_Product_Collection::getFieldsMap();
        if (!empty($options) && is_array($options)) {
            $value = $row->getData($this->getColumn()->getIndex());
            if (is_array($value)) {
                $res = array();
                foreach ($value as $item) {
                    if (isset($options[$item])) {
                        $label = $options[$item];
                        if ($item == GoMage_SeoBooster_Model_Analyzer::LONG_ERROR ||
                            $item == GoMage_SeoBooster_Model_Analyzer::SHORT_ERROR) {
                            $countField = $fieldsMap[$this->getColumn()->getIndex()];
                            $label .= " ({$row->getData($countField)})";
                            $label = $this->escapeHtml($label);
                        } elseif ($item == GoMage_SeoBooster_Model_Analyzer::DUPLICATE_ERROR) {
                            if ($duplicates = $row->getData('duplicate_'. $this->getColumn()->getIndex())) {
                                $duplicatesCount = count($duplicates);
                                $duplicateEntityId = $row->getData('duplicate_entity_id');
                                $label = $this->escapeHtml($label);
                                $label .= " (<a href='". $this->_getDuplicateUrl($this->getColumn()->getIndex(), $duplicateEntityId) ."'>{$duplicatesCount}</a>)";
                            }
                        }
                        $res[] = $label;
                    }
                }
                return implode(', ', $res);
            } elseif (isset($options[$value])) {
                return $this->escapeHtml($options[$value]);
            } elseif (in_array($value, $options)) {
                return $this->escapeHtml($value);
            }
        }
    }

    protected function _getDuplicateUrl($field, $duplicateEntityId)
    {
        return Mage::helper('adminhtml')->getUrl('*/*/duplicates', array('entity_id' => $duplicateEntityId, 'type' => $field));
    }
}
