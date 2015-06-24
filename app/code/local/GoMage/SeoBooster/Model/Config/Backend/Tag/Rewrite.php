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

class GoMage_SeoBooster_Model_Config_Backend_Tag_Rewrite extends Mage_Core_Model_Config_Data
{
    /**
     * Change index process status if value changed
     *
     * @return GoMage_SeoBooster_Model_Config_Backend_Review_Rewrite
     */
    protected function _afterSave()
    {
        parent::_afterSave();
        if ($this->getOldValue() != $this->getValue()) {
            $process = Mage::getSingleton('index/indexer')->getProcessByCode('tag_summary');
            $process->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
        }

        return $this;
    }
}
