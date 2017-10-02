<?php

class Dueclic_Emailchef_TestController extends Mage_Core_Controller_Front_Action
{

    public function getAbandonedCartsAction()
    {

        $fromDate = date("Y-m-d H:i:s", strtotime('-7 days'));
        $toDate   = date("Y-m-d H:i:s", strtotime('-1 days'));

        /**
         * @var $quotes \Mage_Sales_Model_Resource_Quote_Collection
         */

        $quotes = Mage::getResourceModel('sales/quote_collection');

        $quotes->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('converted_at', array('null' => true))
            ->addFieldToFilter('customer_email', array('notnull' => true))
            ->addFieldToFilter('emailchef_sync', array('null' => true));

        /**
         * @var $quote \Mage_Sales_Model_Quote
         */

        foreach ($quotes as $quote) {

            if ($quote->getItemsQty() == 0)
                continue;

            if ($quote->getUpdatedAt() < $fromDate || $quote->getUpdatedAt() > $toDate ) {
                continue;
            }

            $ab = Mage::helper("dueclic_emailchef/abandonedcart");
            print_r($ab->get($quote));

        }

    }

}