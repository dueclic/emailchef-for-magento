<?php

class Dueclic_Emailchef_TestController extends Mage_Core_Controller_Front_Action
{

    public function getTestAction()
    {

        /**
         * @var $website \Mage_Core_Model_Website
         */

        $websites = Mage::app()->getWebsites();
        foreach ($websites as $website){
            print $website->getName();
        }
    }

}