<?php
/**
 * Config.php.
 *
 * Central config model
 */
class Dueclic_Emailchef_Model_Config
{

    public function getEmailChefClass(){
        return dirname(__DIR__) . "/lib/emailchef/class-emailchef.php";
    }

    /**
     * @param $user
     * @param $pass
     *
     * @return \MG_Emailchef
     */

    public function getEmailChefInstance($user, $pass){
        require_once(dirname(__DIR__) . "/lib/emailchef/class-emailchef.php");
        $mgec = \MG_Emailchef::getInstance($user, $pass);
        return $mgec;
    }

    public function getShopLogo(){
    	return Mage::getSingleton('core/design_package')->getSkinBaseUrl().Mage::getStoreConfig('design/header/logo_src');
    }

    public function getVerifyUrl($email){
	    return Mage::getUrl('emailchef/optin/verify/email/'.$email);
    }

	public function getUnsubUrl($email){
		return Mage::getUrl('emailchef/optin/unsub/email/'.$email);
	}

}
