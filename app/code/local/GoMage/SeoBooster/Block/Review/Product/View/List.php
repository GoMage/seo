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
class GoMage_SeoBooster_Block_Review_Product_View_List extends Mage_Review_Block_Product_View_List
{
    public function getReviewUrl($id)
    {
        if (Mage::helper('gomage_seobooster')->canUseProductReviewsUrlRewrite()) {
            return Mage::getModel('gomage_seobooster/review_url')->getUrl($id);
        }

        return Mage::getUrl('*/*/view', array('id' => $id));
    }
}
