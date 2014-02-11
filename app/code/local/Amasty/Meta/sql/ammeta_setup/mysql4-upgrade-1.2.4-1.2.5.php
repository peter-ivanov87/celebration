<?php
/**
 * @author Amasty
 * @copyright Copyright (c) 2009-2012 Amasty (http://www.amasty.com)
 */
/** @var Mage_Core_Model_Resource_Setup $installer */
$installer  = $this->startSetup();
$connection = $installer->getConnection();

//if module was already installed
if (Mage::getModel('core/resource_resource')->getDataVersion('ammeta_setup')) {
//config keys
	$rootConfigKeys = array(
		'ammeta/product/title'             => array('product_meta_title', 'sub_product_meta_title'),
		'ammeta/product/description'       => array('product_meta_description', 'sub_product_meta_description'),
		'ammeta/product/keywords'          => array('product_meta_keyword', 'sub_product_meta_keyword'),
		'ammeta/product/full_description'  => array('product_description', 'sub_product_description'),
		'ammeta/product/short_description' => array('product_short_description', 'sub_product_short_description'),

		'ammeta/cat/meta_title'            => array('sub_cat_meta_title', 'sub_cat_meta_title'),
		'ammeta/cat/meta_description'      => array('sub_cat_meta_description', 'sub_cat_meta_description'),
		'ammeta/cat/meta_keywords'         => array('sub_cat_meta_keywords', 'sub_cat_meta_keywords'),
		'ammeta/cat/description'           => array('sub_cat_description', 'sub_cat_description'),

		'ammeta/cat/sub_meta_title'        => array('sub_cat_meta_title', 'sub_cat_meta_title'),
		'ammeta/cat/sub_meta_description'  => array('sub_cat_meta_description', 'sub_cat_meta_description'),
		'ammeta/cat/sub_meta_keywords'     => array('sub_cat_meta_keywords', 'sub_cat_meta_keywords'),
		'ammeta/cat/sub_description'       => array('sub_cat_description', 'sub_cat_description')
	);

	$newColumnsArray = array(
		'store_id'                      => 'INT NOT NULL DEFAULT 0',
		'is_custom'                     => 'tinyint(1) NOT NULL DEFAULT 0',
		'custom_url'                    => 'VARCHAR(255) NOT NULL',
		'priority'                      => 'INT NOT NULL DEFAULT 0',

		'custom_meta_title'             => 'VARCHAR(255)',
		'custom_meta_keywords'          => 'TEXT',
		'custom_meta_description'       => 'TEXT',
		'custom_canonical_url'          => 'VARCHAR(255)',
		'custom_robots'                 => 'SMALLINT(1) UNSIGNED DEFAULT 0',
		'custom_h1_tag'                 => 'VARCHAR(255)',
		'custom_in_page_text'           => 'TEXT',

		'cat_meta_title'                => 'VARCHAR(255)',
		'cat_meta_description'          => 'TEXT',
		'cat_meta_keywords'             => 'TEXT',
		'cat_h1_tag'                    => 'VARCHAR(255)',
		'cat_description'               => 'TEXT',
		'cat_image_alt'                 => 'VARCHAR(255)',
		'cat_image_title'               => 'VARCHAR(255)',
		'cat_after_product_text'        => 'TEXT',

		'sub_cat_meta_title'            => 'VARCHAR(255)',
		'sub_cat_meta_description'      => 'TEXT',
		'sub_cat_meta_keywords'         => 'TEXT',
		'sub_cat_h1_tag'                => 'VARCHAR(255)',
		'sub_cat_description'           => 'TEXT',
		'sub_cat_image_alt'             => 'VARCHAR(255)',
		'sub_cat_image_title'           => 'VARCHAR(255)',
		'sub_cat_after_product_text'    => 'TEXT',

		'product_meta_title'            => 'VARCHAR(255)',
		'product_meta_keyword'          => 'TEXT',
		'product_meta_description'      => 'TEXT',
		'product_h1_tag'                => 'VARCHAR(255)',
		'product_short_description'     => 'TEXT',
		'product_description'           => 'TEXT',

		'sub_product_meta_title'        => 'VARCHAR(255)',
		'sub_product_meta_keyword'      => 'TEXT',
		'sub_product_meta_description'  => 'TEXT',
		'sub_product_h1_tag'            => 'VARCHAR(255)',
		'sub_product_short_description' => 'TEXT',
		'sub_product_description'       => 'TEXT'
	);

	$installer->run("ALTER TABLE `{$this->getTable('ammeta/config')}` MODIFY `category_id` MEDIUMINT(9) DEFAULT NULL;");

//add new columns
	foreach ($newColumnsArray as $columnName => $columnDef) {
		$installer->run("ALTER TABLE `{$this->getTable('ammeta/config')}` ADD COLUMN  `$columnName` $columnDef;");
	}

//update product data
	$productKeys = array(
		'title'             => 'product_meta_title',
		'keywords'          => 'product_meta_keyword',
		'description'       => 'product_meta_description',
		'short_description' => 'product_short_description',
		'full_description'  => 'product_description'
	);

	foreach ($productKeys as $oldProductColumn => $newProductColumn) {
		$installer->run("UPDATE `{$this->getTable('ammeta/config')}` SET $newProductColumn = $oldProductColumn");
		$installer->run("ALTER TABLE `{$this->getTable('ammeta/config')}` DROP COLUMN  `$oldProductColumn`;");
	}

	$installer->run("ALTER TABLE `{$this->getTable('ammeta/config')}` DROP COLUMN  `apply_for_child`;");

//separate all data by stores
	$productColumnsString = implode(',', $productKeys) . ', category_id';
	foreach ($connection->fetchAll("SELECT * FROM `{$this->getTable('ammeta/config')}`") as $item) {
		$stores = explode(',', $item['stores']);
		foreach ($stores as $itemStoreId) {
			if (! empty($itemStoreId)) {
				$installer->run(
					"INSERT INTO `{$this->getTable(
						'ammeta/config'
					)}` ($productColumnsString, store_id) (SELECT $productColumnsString, $itemStoreId FROM `{$this->getTable(
						'ammeta/config'
					)}` WHERE `config_id` = {$item['config_id']});"
				);
			}
		}

		$installer->run("DELETE FROM `{$this->getTable('ammeta/config')}` WHERE `config_id` = {$item['config_id']};");
	}

//add data from config
	Mage::app()->reinitStores();
	foreach (Mage::app()->getStores() as $store) {
		$exists             = false;
		$storeCategories    = array(0);
		$parentCategoryId   = $store->getRootCategoryId();
		$category           = Mage::getModel('catalog/category');
		$categoryCollection = $category->getCategories($parentCategoryId, 1, false, true);

		foreach ($categoryCollection as $c) {
			$storeCategories[] = $c->getId();
		}

		foreach ($rootConfigKeys as $oldKey => $newColumns) {
			$oldKeyQuoted = $connection->quote($oldKey);
			$storeId      = $store->getId();
			$scope        = $connection->quote($store->getCode() != 'default' ? 'stores' : 'default');
			$storeConfig  = $store->getCode() != 'default' ? (int) $storeId : 0;

			$cnfValue = $connection->fetchOne("
				SELECT value FROM core_config_data WHERE path = $oldKeyQuoted AND scope_id = $storeConfig AND scope = $scope
			");

			//get data only for default store
			if (! $cnfValue && ! $storeConfig) {
				continue;
			}

			if (! $cnfValue) {
				$cnfValue = (string) Mage::getConfig()->getNode('default/' . $oldKey);
			}

			if ($cnfValue) {
				$storeValue = (int) $storeConfig;
				$cnfValue   = $connection->quote($cnfValue);

				foreach ($storeCategories as $itemCategory) {
					foreach ($newColumns as $newColumn) {
						if (! $connection->fetchOne("
						SELECT COUNT(`config_id`)
						FROM `{$this->getTable('ammeta/config')}`
						WHERE `store_id` = $storeValue AND `category_id` = $itemCategory AND `is_custom` = false
					")
						) {
							$installer->run("
							INSERT INTO `{$this->getTable('ammeta/config')}`
							(`{$newColumn}`, `store_id`, `category_id`)
							VALUES
							($cnfValue, $storeValue, $itemCategory);");
						} else {
							$installer->run("UPDATE `{$this->getTable('ammeta/config')}`
							SET store_id = $storeValue, `{$newColumn}` = $cnfValue
							WHERE `store_id` = $storeValue AND `category_id` = $itemCategory AND `is_custom` = false;");
						}
					}
				}
			}
		}
	}
}


$installer->endSetup();