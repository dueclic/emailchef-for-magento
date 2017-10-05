<?php

class Dueclic_Emailchef_TestController extends Mage_Core_Controller_Front_Action
{

    public function getTestAction()
    {

        /**
         * @var $helper \Dueclic_Emailchef_Helper_Customer
         */

        $helper = Mage::helper("dueclic_emailchef/customer");
	    die(var_dump($helper->getCustomersData()));
    }

}