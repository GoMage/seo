<?php

/**
 * GoMage Seo Booster Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2013-2015 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use/
 * @version      Release: 1.3.0
 * @since        Available since Release 1.0.0
 */
class GoMage_SeoBoosterBridge_Block_Page_Html_Pager extends GoMage_Navigation_Block_Html_Pager
{
    /**
     * Returns url model class name
     *
     * @return string
     */
    protected function _getUrlModelClass()
    {
        return 'gomage_seobooster/url';
    }

    public function getPagerUrl($params = array())
    {
        if ($this->isAjaxPager()) {
            $params['ajax'] = 1;
        } else {
            $params['ajax'] = null;
        }

        $urlParams                 = array();
        $urlParams['_nosid']       = true;
        $urlParams['_current']     = true;
        $urlParams['_escape']      = true;
        $urlParams['_use_rewrite'] = true;
        $urlParams['_query']       = $params;

        return Mage::helper('gomage_seobooster/layered')->getUrl('*/*/*', $urlParams);
    }

}