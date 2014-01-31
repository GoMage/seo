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
 * @category   GoMage
 * @package    GoMage_SeoBooster
 * @subpackage Model
 * @author     Roman Bublik <rb@gomage.com>
 */
class GoMage_SeoBooster_Model_Resource_Tag_Url extends Mage_Core_Model_Resource_Db_Abstract
{
    const TAG_TABLE = 'tag/tag';

    /**
     * Initialize table
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('core/url_rewrite', 'url_rewrite_id');
    }

    /**
     * Check tag url key unique
     *
     * @param string $urlKey Url Key
     * @param int    $tagId  Tag Id
     * @return string
     */
    public function checkUrlKeyUnique($urlKey, $tagId = null)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select();

        $select->from($this->getTable(self::TAG_TABLE))
            ->where("url_key = ?", $urlKey);
        if (!is_null($tagId)) {
            $select->where("tag_id != ?", $tagId);
        }

        return $adapter->fetchOne($select);
    }
}
