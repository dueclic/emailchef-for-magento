<?php

class Dueclic_Emailchef_TestController extends Mage_Core_Controller_Front_Action {

    public function getAbandonedCartsAction(){

        $quotes = Mage::getResourceModel('sales/quote_collection')
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('converted_at', array('null' => true))
            ->addFieldToFilter('customer_email', array('notnull' => true))
            ->addFieldToFilter('emailchef_sync', array('null' => true ))
            ->addFieldToFilter('update_at', array('gteq' => '(NOW() - INTERVAL 7 DAY'))
            ->addFieldToFilter('update_at', array('lteq' => ''));


        foreach ($quotes as $quote) {

            $oItems = Mage::getModel( 'sales/quote_item' )
                ->getCollection()
                ->setQuote( $quote );
            foreach( $oItems as $oItem )
            {
                var_dump( $oItem->getProduct()->getId() );
            }

        }

    }

}