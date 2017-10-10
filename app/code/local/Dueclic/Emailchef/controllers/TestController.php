<?php

class Dueclic_Emailchef_TestController extends Mage_Core_Controller_Front_Action
{

    public function getTestAction()
    {

        $customer_id = $this->getRequest()->getParam("cid");
        $store_id = $this->getRequest()->getParam("sid");

        /**
         * @var $customer \Mage_Customer_Model_Customer
         */

        $customer = Mage::getModel("customer/customer")
            ->load($customer_id);

        $website_id = $customer->getData("website_id");
        $store_id = $customer->getData("store_id");

        die(
            "Website ID ".$website_id." Store ID ".$store_id
        );

    }

}