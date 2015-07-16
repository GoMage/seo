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
 * @since        Available since Release 1.2.0
 */
class GoMage_SeoBooster_Model_Resource_Sitemap_Cms_Page extends Mage_Sitemap_Model_Resource_Cms_Page
{
	/**
     * Retrieve cms page collection array
     *
     * @param unknown_type $storeId
     * @return array
     */
    public function getCollection($storeId)
    {
        $pages	= array();
        $select	= $this->_getWriteAdapter()->select()
            ->from(array('main_table' => $this->getMainTable()), 
				array(
					$this->getIdFieldName(), 
					'identifier AS url',
					'exclude_from_sitemap'
				)
			)
            ->join(
                array('store_table' => $this->getTable('cms/page_store')),
                'main_table.page_id=store_table.page_id',
                array()
            )
            ->where('main_table.is_active=1')
            ->where('store_table.store_id IN(?)', array(0, $storeId));
       
	    $query = $this->_getWriteAdapter()->query($select);
       
	    while ($row = $query->fetch()) {
            if ($row['url'] == Mage_Cms_Model_Page::NOROUTE_PAGE_ID) {
                continue;
            }
			
            $page = $this->_preparePage($row);
            $pages[$page->getId()] = $page;
        }

        return $pages;
    }

    /**
     * Prepare page object
     *
     * @param array $data
     * @return Varien_Object
     */
    protected function _preparePage(array $data)
    {
        $page = new Varien_Object();
        $page->setId($data[$this->getIdFieldName()]);
        $page->setUrl($data['url']);

		$excludeFromSitemap = !empty($data['exclude_from_sitemap']) ? $data['exclude_from_sitemap'] : 0;
        $page->setExcludeFromSitemap($excludeFromSitemap);

        return $page;
    }
}