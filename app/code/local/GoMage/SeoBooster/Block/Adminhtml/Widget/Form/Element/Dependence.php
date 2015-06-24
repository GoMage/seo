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

class GoMage_SeoBooster_Block_Adminhtml_Widget_Form_Element_Dependence extends Mage_Adminhtml_Block_Widget_Form_Element_Dependence
{
    const MULTIPLE_VALUE_PREFIX = 'multiple-';

    /**
     * Field dependences JSON map generator
     *
     * @return string
     */
    protected function _getDependsJson()
    {
        $result = array();
		
        foreach ($this->_depends as $to => $row) {
            foreach ($row as $from => $value) {
				if (is_array($value)) {
                	foreach ($value as $i => $v) {
						if (strpos($v, self::MULTIPLE_VALUE_PREFIX) !== false) {
							$v = str_replace(self::MULTIPLE_VALUE_PREFIX, '', $v);
							$v = explode('-', $v);
						}
						
						$result[$this->_fields[$to]][$this->_fields[$from]][$i] = $v;
					}
				} else {
					if (strpos($value, self::MULTIPLE_VALUE_PREFIX) !== false) {
						$value = str_replace(self::MULTIPLE_VALUE_PREFIX, '', $value);
						$value = explode('-', $value);
					}
					
					$result[$this->_fields[$to]][$this->_fields[$from]] = $value;
				}
            }
        }
		
        return Mage::helper('core')->jsonEncode($result);
    }

    /**
     * HTML output getter
     * @return string
     */
    protected function _toHtml()
    {
        if (!Mage::helper('gomage_seobooster')->getIsAnymoreVersion(1, 6)) {
            return parent::_toHtml();
        }

        if (!$this->_depends) {
            return '';
        }
        return "<script type=\"text/javascript\">
        FormElementDependenceController.prototype.trackChange = function(e, idTo, valuesFrom) {
            var shouldShowUp = true;
            for (var idFrom in valuesFrom) {
                var from = $(idFrom);
                if (valuesFrom[idFrom] instanceof Array) {
                    if (!from || valuesFrom[idFrom].indexOf(from.value) == -1) {
                        shouldShowUp = false;
                    }
                } else {
                    if (!from || from.value != valuesFrom[idFrom]) {
                        shouldShowUp = false;
                    }
                }
            }

            if (shouldShowUp) {
                var currentConfig = this._config;
                $(idTo).up(this._config.levels_up).select('input', 'select', 'td').each(function (item) {
                    if ((!item.type || item.type != 'hidden') && !($(item.id+'_inherit') && $(item.id+'_inherit').checked)
                        && !(currentConfig.can_edit_price != undefined && !currentConfig.can_edit_price)) {
                        item.disabled = false;
                    }
                });
                $(idTo).up(this._config.levels_up).show();
            } else {
                $(idTo).up(this._config.levels_up).select('input', 'select', 'td').each(function (item){
                if ((!item.type || item.type != 'hidden') && !($(item.id+'_inherit') && $(item.id+'_inherit').checked)) {
                    item.disabled = true;
                }
                });
                $(idTo).up(this._config.levels_up).hide();
            }
        }
        new FormElementDependenceController("
        . $this->_getDependsJson()
        . ($this->_configOptions ? ", " . Mage::helper('core')->jsonEncode($this->_configOptions) : '')
        . "); </script>";
    }
}
