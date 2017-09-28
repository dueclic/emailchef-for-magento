<?php

class Dueclic_Emailchef_Model_Observer {

    public function maybeSyncEmailchef(){
        Mage::log("Dati emailchef salvati con successo.", 3);
        $url = Mage::getUrl('myrouter/adminhtml_test/validate');
        Mage::app()->getResponse()->setRedirect($url);
    }

}