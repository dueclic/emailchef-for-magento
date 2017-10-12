<?php

class Dueclic_Emailchef_Model_System_Config_Source_Dropdown_List {
    public function toOptionArray() {

        $websiteCode = Mage::app()->getRequest()->getParam('website');
        $storeCode = Mage::app()->getRequest()->getParam('store');

        if(isset($storeId) && $storeId != FALSE) {
            $storeId = $storeId;
        }
        elseif($storeCode) {
            $storeId = Mage::app()->getStore($storeCode)->getId();
        }
        elseif($websiteCode) {
            $storeId = Mage::app()
                ->getWebsite($websiteCode)
                ->getDefaultGroup()
                ->getDefaultStoreId()
            ;
        }
        else {
            $storeId = NULL;
        }

        /**
         * @var $config \Dueclic_Emailchef_Model_Config
         */

        $config = Mage::getModel("dueclic_emailchef/config");

        $username = Mage::getStoreConfig( 'emailchef/general/username', $storeId );
        $password = Mage::getStoreConfig( 'emailchef/general/password', $storeId );

        if (!empty($username) && !empty($password)){
            $emailchef = $config->getEmailChefInstance($username, $password);

            if ($emailchef->isLogged()) {
                return $emailchef->get_lists();
            }

            return array(
                array(
                    'value' => -1,
                    'label' => Mage::helper("dueclic_emailchef")->__("eMailChef account credentials are wrong."),
                ),
            );

        }

        return array(
            array(
                'value' => -1,
                'label' => Mage::helper("dueclic_emailchef")->__("Provide valid account credentials."),
            ),
        );
    }
}
