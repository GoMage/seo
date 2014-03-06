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
 * @var $this Mage_Eav_Model_Entity_Setup
 */

$installer = $this;
$installer->startSetup();

try {
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
} catch (Exception $e) {
    Mage::logException($e);
}

$installer->endSetup();
