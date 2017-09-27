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
        require(dirname(__DIR__) . "/lib/emailchef/class-emailchef.php");
        $mgec = new MG_Emailchef($user, $pass);
        return $mgec;
    }

}
