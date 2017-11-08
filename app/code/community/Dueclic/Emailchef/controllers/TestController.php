<?php

class Dueclic_Emailchef_TestController extends Mage_Core_Controller_Front_Action
{

    public function getCustomerAction()
    {

        $customer_id = $this->getRequest()->getParam("cid");

        /**
         * @var $customer \Dueclic_Emailchef_Helper_Customer
         */

        $customer = Mage::helper("dueclic_emailchef/customer");
        $data = $customer->getCustomerData($customer_id);

        die("<pre>".var_export($data, true)."</pre>");

    }

    public function getCustomersAction(){

        $store_ids = $this->getRequest()->getParam("stores");
        $store_ids = explode(",", $store_ids);

        /**
         * @var $customer \Dueclic_Emailchef_Helper_Customer
         */

        $customer = Mage::helper("dueclic_emailchef/customer");
        $data = $customer->getCustomersData("noinsert", $store_ids);
        die("<pre>".var_export($data, true)."</pre>");

    }

    public function getWebsiteAction(){

        $website_id = $this->getRequest()->getParam("website");

        /**
         * @var $customer \Dueclic_Emailchef_Helper_Customer
         */

        $customer = Mage::helper("dueclic_emailchef/customer");
        $data = $customer->getCustomersByWebsiteId($website_id);
        die("<pre>".var_export($data, true)."</pre>");

    }

}