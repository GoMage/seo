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
 * Tag Indexer Model
 *
 * @category   GoMage
 * @package    GoMage_SeoBooster
 * @subpackage Model
 * @author     Roman Bublik <rb@gomage.com>
 */
class GoMage_SeoBooster_Model_Resource_Tag_Indexer_Summary extends Mage_Tag_Model_Resource_Indexer_Summary
{
    /**
     * Reindex all tags
     *
     * @return Mage_Tag_Model_Resource_Indexer_Summary
     */
    public function reindexAll()
    {
        parent::reindexAll();
        Mage::getModel('gomage_seobooster/tag_url')->refreshTagsRewrites();

        return $this;
    }
}
