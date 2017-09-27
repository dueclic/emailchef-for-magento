<?php

class Dueclic_Emailchef_Model_System_Config_Source_Dropdown_List {
    public function toOptionArray() {

        /**
         * @var $config \Dueclic_Emailchef_Model_Config
         */

        $config = Mage::getModel("dueclic_emailchef/config");

        $username = Mage::getStoreConfig( 'tab1/general/username' );
        $password = Mage::getStoreConfig( 'tab1/general/password' );

        if (!empty($username) && !empty($password)){
            $emailchef = $config->getEmailChefInstance($username, $password);

            if ($emailchef->isLogged())
                return $emailchef->get_lists();

            return array(
                array(
                    'value' => -1,
                    'label' => "I dati di accesso eMailChef sono errati.",
                ),
            );

        }

        return array(
            array(
                'value' => -1,
                'label' => "Effettua il login con eMailChef prima.",
            ),
        );
    }
}
