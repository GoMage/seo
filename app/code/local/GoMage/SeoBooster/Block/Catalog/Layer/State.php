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

class GoMage_SeoBooster_Block_Catalog_Layer_State extends Mage_Catalog_Block_Layer_State
{
    /**
     * Retrieve Clear Filters URL
     *
     * @return string
     */
    public function getClearUrl()
    {
        $filterState = array();
        foreach ($this->getActiveFilters() as $item) {
            $filterState[$item->getFilter()->getRequestVar()] = $item->getFilter()->getCleanValue();
        }
        $queryIndex = Mage::helper('gomage_seobooster/layered')->getSeparator() ? '_layered_query_params' : '_query';
        $params['_current']     = true;
        $params['_use_rewrite'] = true;
        $params['_use_layer_rewrite'] = false;
        $params[$queryIndex]    = $filterState;
        $params['_escape']      = true;
        
        return Mage::helper('gomage_seobooster/layered')->getUrl('*/*/*', $params);
    }
}
