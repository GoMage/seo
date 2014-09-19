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

/**
 * @var $this Mage_Core_Model_Resource_Setup
 */

$installer = $this;
$installer->startSetup();

try {
    /**
     * Add canonical product attributes
     */
    $attributesOptions = array(
        'canonical_url_type' => array(
            'label' => 'Canonical URL',
            'source' => 'gomage_seobooster/config_source_canonical_catalog_product_type',
        ),
        'canonical_url_store' => array(
            'label' => 'Cross-Domain Canonical URL',
            'source' => 'gomage_seobooster/config_source_canonical_catalog_storeurl',
        )
    );

    foreach ($attributesOptions as $_code => $_options) {
        $installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, $_code, array_merge($_options, array(
            'type'              => 'int',
            'input'             => 'select',
            'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
            'visible'           => false,
            'required'          => false,
            'user_defined'      => true,
            'default'           => 0,
            'searchable'        => false,
            'filterable'        => false,
            'comparable'        => false,
            'visible_on_front'  => false,
            'unique'            => false,
            'group'             => 'Meta Information',
            'is_configurable'   => 0
        )));

        $this->updateAttribute(Mage_Catalog_Model_Product::ENTITY,
            $_code,
            'is_configurable',
            0
        );
    }

    /**
     * Add canonical store attribute for category
     */
    $installer->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'canonical_url_store', array(
            'type' => 'int',
            'label' => "Cross-Domain Canonical URL",
            'input' => 'select',
            'source' => 'gomage_seobooster/config_source_canonical_catalog_storeurl',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
            'visible' => true,
            'required' => false,
            'default' => 0,
            'group' => 'Meta Information',
        ));

    /* Add url rewrite id to tag */
    $installer->getConnection()->addColumn(
        $installer->getTable('tag/tag'),
        'url_rewrite_id',
        "INTEGER(12) UNSIGNED DEFAULT NULL"
    );

    /* Add url key */
    $installer->getConnection()->addColumn(
        $installer->getTable('tag/tag'),
        'url_key',
        "VARCHAR(255) DEFAULT NULL"
    );


    $attributesOptions = array(
        'robots' => array(
            'label'  => 'Robots',
            'source' => 'gomage_seobooster/config_source_robots',
            'type'   => 'varchar'
        ),
        'exclude_from_sitemap' => array(
            'label'  => 'Exclude from XML Sitemap',
            'source' => 'eav/entity_attribute_source_boolean',
            'type'   => 'int'
        )
    );

    foreach ($attributesOptions as $_code => $_options) {
        $installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, $_code, array_merge($_options, array(
                    'input'             => 'select',
                    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                    'visible'           => false,
                    'required'          => false,
                    'user_defined'      => true,
                    'default'           => 0,
                    'searchable'        => false,
                    'filterable'        => false,
                    'comparable'        => false,
                    'visible_on_front'  => false,
                    'unique'            => false,
                    'group'             => 'Meta Information',
                    'is_configurable'   => 0
                )));

        $this->updateAttribute(Mage_Catalog_Model_Product::ENTITY,
            $_code,
            'is_configurable',
            0
        );

        $installer->addAttribute(Mage_Catalog_Model_Category::ENTITY, $_code, array_merge($_options, array(
                    'input' => 'select',
                    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                    'visible' => true,
                    'required' => false,
                    'default' => 0,
                    'group' => 'Meta Information',
                )));
    }

    /* Add url rewrite id to tag */
    $installer->getConnection()->addColumn(
        $installer->getTable('cms/page'),
        'robots',
        "VARCHAR(255) NOT NULL"
    );
    $installer->getConnection()->addColumn(
        $installer->getTable('cms/page'),
        'exclude_from_sitemap',
        "SMALLINT(1) UNSIGNED NOT NULL DEFAULT 0"
    );

    $installer->getConnection()->addColumn(
        $installer->getTable('cms/page'),
        'canonical_url_store',
        "INT(11) UNSIGNED NOT NULL DEFAULT 0"
    );

    /* Add url key */
    $installer->getConnection()->addColumn(
        $installer->getTable('review/review'),
        'url_rewrite_id',
        "INTEGER(12) UNSIGNED DEFAULT NULL"
    );

    $table = $installer->getConnection()->newTable($installer->getTable('gomage_seobooster/analyzer_product'))
        ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 12, array(
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary'  => true,
            ), "Entity Id")
        ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 12, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Product Id')
        ->addColumn('name_chars_count', Varien_Db_Ddl_Table::TYPE_INTEGER, 12, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Product Name Chars count')
        ->addColumn('description_chars_count', Varien_Db_Ddl_Table::TYPE_INTEGER, 12, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Product Description Chars count')
        ->addColumn('meta_title_chars_count', Varien_Db_Ddl_Table::TYPE_INTEGER, 12, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Meta title Chars count')
        ->addColumn('meta_description_chars_count', Varien_Db_Ddl_Table::TYPE_INTEGER, 12, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Meta Description Chars count')
        ->addColumn('meta_keyword_chars_count', Varien_Db_Ddl_Table::TYPE_INTEGER, 12, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Meta Keywords chars count')
        ->addColumn('meta_keyword_qty', Varien_Db_Ddl_Table::TYPE_INTEGER, 12, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Meta Keywords qty');

    $installer->getConnection()->createTable($table);

    $table = $installer->getConnection()->newTable($installer->getTable('gomage_seobooster/analyzer_product_duplicates'))
        ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 12, array(
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary'  => true,
            ), "Entity Id")
        ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 12, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Product Id')
        ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 1024, array(
                'unsigned' => true,
                'nullable' => true,
            ), 'Product Name')
        ->addColumn('description', Varien_Db_Ddl_Table::TYPE_VARCHAR, 1024, array(
                'unsigned' => true,
                'nullable' => true,
            ), 'Product Description')
        ->addColumn('meta_title', Varien_Db_Ddl_Table::TYPE_VARCHAR, 1024, array(
                'unsigned' => true,
                'nullable' => true,
            ), 'Product Meta Title')
        ->addColumn('meta_description', Varien_Db_Ddl_Table::TYPE_VARCHAR, 1024, array(
                'unsigned' => true,
                'nullable' => true,
            ), 'Product Meta Description')
        ->addColumn('meta_keyword', Varien_Db_Ddl_Table::TYPE_VARCHAR, 1024, array(
                'unsigned' => true,
                'nullable' => true,
            ), 'Product Meta Keyword');
    $installer->getConnection()->createTable($table);

    $table = $installer->getConnection()->newTable($installer->getTable('gomage_seobooster/analyzer_category'))
        ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 12, array(
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary'  => true,
            ), "Entity Id")
        ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 12, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Page Id')
        ->addColumn('name_chars_count', Varien_Db_Ddl_Table::TYPE_INTEGER, 12, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Category Name Chars count')
        ->addColumn('description_chars_count', Varien_Db_Ddl_Table::TYPE_INTEGER, 12, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Category Description Chars count')
        ->addColumn('meta_title_chars_count', Varien_Db_Ddl_Table::TYPE_INTEGER, 12, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Meta title Chars count')
        ->addColumn('meta_description_chars_count', Varien_Db_Ddl_Table::TYPE_INTEGER, 12, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Meta Description Chars count')
        ->addColumn('meta_keyword_chars_count', Varien_Db_Ddl_Table::TYPE_INTEGER, 12, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Meta Keywords chars count')
        ->addColumn('meta_keyword_qty', Varien_Db_Ddl_Table::TYPE_INTEGER, 12, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Meta Keywords qty');

    $installer->getConnection()->createTable($table);

    $table = $installer->getConnection()->newTable($installer->getTable('gomage_seobooster/analyzer_category_duplicates'))
        ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 12, array(
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary'  => true,
            ), "Entity Id")
        ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 12, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Category Id')
        ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 1024, array(
                'unsigned' => true,
                'nullable' => true,
            ), 'Category Name')
        ->addColumn('description', Varien_Db_Ddl_Table::TYPE_VARCHAR, 1024, array(
                'unsigned' => true,
                'nullable' => true,
            ), 'Category Description')
        ->addColumn('meta_title', Varien_Db_Ddl_Table::TYPE_VARCHAR, 1024, array(
                'unsigned' => true,
                'nullable' => true,
            ), 'Category Meta Title')
        ->addColumn('meta_description', Varien_Db_Ddl_Table::TYPE_VARCHAR, 1024, array(
                'unsigned' => true,
                'nullable' => true,
            ), 'Category Meta Description')
        ->addColumn('meta_keyword', Varien_Db_Ddl_Table::TYPE_VARCHAR, 1024, array(
                'unsigned' => true,
                'nullable' => true,
            ), 'Category Meta Keyword');
    $installer->getConnection()->createTable($table);


    $table = $installer->getConnection()->newTable($installer->getTable('gomage_seobooster/analyzer_page'))
        ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 12, array(
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary'  => true,
            ), "Entity Id")
        ->addColumn('page_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 12, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Page Id')
        ->addColumn('name_chars_count', Varien_Db_Ddl_Table::TYPE_INTEGER, 12, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Page Name Chars count')
        ->addColumn('meta_description_chars_count', Varien_Db_Ddl_Table::TYPE_INTEGER, 12, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Meta Description Chars count')
        ->addColumn('meta_keyword_chars_count', Varien_Db_Ddl_Table::TYPE_INTEGER, 12, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Meta Keywords chars count')
        ->addColumn('meta_keyword_qty', Varien_Db_Ddl_Table::TYPE_INTEGER, 12, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Meta Keywords qty');

    $installer->getConnection()->createTable($table);

    $table = $installer->getConnection()->newTable($installer->getTable('gomage_seobooster/analyzer_page_duplicates'))
        ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 12, array(
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary'  => true,
            ), "Entity Id")
        ->addColumn('page_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 12, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Page Id')
        ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 1024, array(
                'unsigned' => true,
                'nullable' => true,
            ), 'Page Name')
        ->addColumn('meta_description', Varien_Db_Ddl_Table::TYPE_VARCHAR, 1024, array(
                'unsigned' => true,
                'nullable' => true,
            ), 'Page Meta Description')
        ->addColumn('meta_keyword', Varien_Db_Ddl_Table::TYPE_VARCHAR, 1024, array(
                'unsigned' => true,
                'nullable' => true,
            ), 'Page Meta Keyword');
    $installer->getConnection()->createTable($table);

} catch (Exception $e) {
    Mage::logException($e);
}

$installer->endSetup();
