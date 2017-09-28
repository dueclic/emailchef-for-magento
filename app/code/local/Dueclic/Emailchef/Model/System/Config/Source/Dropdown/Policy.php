<?php

class Dueclic_Emailchef_Model_System_Config_Source_Dropdown_Policy
{

    public function toOptionArray()
    {

        /**
         * @var $config \Dueclic_Emailchef_Model_Config
         */

        $config = Mage::getModel("dueclic_emailchef/config");

        $username = Mage::getStoreConfig('emailchef/general/username');
        $password = Mage::getStoreConfig('emailchef/general/password');

        if ( ! empty($username) && ! empty($password)) {
            $emailchef = $config->getEmailChefInstance($username, $password);

            if ($emailchef->isLogged()) {

                if ($emailchef->get_policy() === 'premium') {

                    return array(
                        array(
                            'value' => 'dopt',
                            'label' => 'Double opt-in',
                        ),
                        array(
                            'value' => 'sopt',
                            'label' => 'Single opt-in',
                        ),
                    );

                }

            }
        }

        return array(
            array(
                'value' => 'dopt',
                'label' => 'Double opt-in',
            ),
        );

    }
}
