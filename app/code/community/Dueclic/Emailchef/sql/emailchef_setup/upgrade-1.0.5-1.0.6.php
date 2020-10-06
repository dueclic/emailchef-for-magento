<?php

/**
 * @var $installer Mage_Core_Model_Resource_Setup
 */


$installer = $this;
$installer->startSetup();
try {
    $installer->run(
        "CREATE TABLE IF NOT EXISTS `{$installer->getTable('emailchef_abcart_synced')}` (`id` int(11) unsigned NOT NULL auto_increment,`last_date_sync` DATETIME NOT NULL,PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1"
    );
}
catch (\Exception $e) {
        Mage::logException($e);
}

$installer->endSetup();

