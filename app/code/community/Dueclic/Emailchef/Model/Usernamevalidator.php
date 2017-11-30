<?php

class Dueclic_EmailChef_Model_Usernamevalidator  extends Mage_Core_Model_Config_Data
{
    public function save()
    {
        /*$value = $this->getValue();
        if (strlen($value) == 0) {
            Mage::throwException(__('Please fill the username.'));
        }

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            Mage::throwException(__('Email is not in the right format.'));
        }*/

        return parent::save();
    }
}
