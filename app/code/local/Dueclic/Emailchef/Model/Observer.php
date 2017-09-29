<?php

class Dueclic_Emailchef_Model_Observer {

    public function maybeSyncEmailchef(){
        Mage::log("Dati emailchef salvati con successo.", Zend_Log::INFO);
        $url = Mage::getUrl('myrouter/adminhtml_test/validate');
        Mage::app()->getResponse()->setRedirect($url);
    }

}