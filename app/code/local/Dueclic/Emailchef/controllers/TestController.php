<?php

class Dueclic_Emailchef_TestController extends Mage_Core_Controller_Front_Action
{

    public function getTestAction()
    {
        Mage::getModel( 'core/config' )->saveConfig( 'emailchef/general/syncevent', 0 );
        Mage::getModel( 'core/config' )->saveConfig( 'emailchef/general/username', "" );
        Mage::getModel( 'core/config' )->saveConfig( 'emailchef/general/password', "" );
        Mage::getModel( 'core/config' )->saveConfig( 'emailchef/general/list', "" );
        Mage::getModel( 'core/config' )->saveConfig( 'emailchef/general/policy', "" );
    }

}