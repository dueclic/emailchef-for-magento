<?php

class Dueclic_Emailchef_Model_System_Config_Source_Dropdown_Policy {
    public function toOptionArray() {
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
