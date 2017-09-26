<?php

class Dueclic_Emailchef_Model_System_Config_Source_Dropdown_List {
    public function toOptionArray() {

        return array(
            array(
                'value' => 'dopt',
                'label' => Mage::getBaseUrl(),
            ),
            array(
                'value' => 'sopt',
                'label' => 'Single opt-in',
            ),
        );
    }
}
