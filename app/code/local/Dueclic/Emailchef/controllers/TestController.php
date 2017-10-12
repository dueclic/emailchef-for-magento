<?php

class Dueclic_Emailchef_TestController extends Mage_Core_Controller_Front_Action
{

    public function getTestAction()
    {

        $customer_id = $this->getRequest()->getParam("cid");

        /**
         * @var $customer \Dueclic_Emailchef_Helper_Customer
         */

        $customer = Mage::helper("dueclic_emailchef/customer");
        $data = $customer->getCustomerData($customer_id);

        die("<pre>".var_export($data, true)."</pre>");

    }

}