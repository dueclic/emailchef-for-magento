<?php

$installer = $this;
$installer->startSetup();

if ( ! $installer->getConnection("core_read")->tableColumnExists(
    $installer->getTableName('sales_flat_quote'), 'emailchef_sync'
)
) {
    $installer->getConnection("core_write")->addColumn(
        $installer->getTableName('sales_flat_quote'), 'emailchef_sync',
        "INT( 1 ) NULL"
    );
}

$installer->endSetup();
