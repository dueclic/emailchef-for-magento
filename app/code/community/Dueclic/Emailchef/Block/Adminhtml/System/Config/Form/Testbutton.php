<?php

/**
 * Self-test button for connection details in system configuration.
 */
class Dueclic_Emailchef_Block_Adminhtml_System_Config_Form_Testbutton
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    private function login_emailchef()
    {

        $verify_login = $this->__(
            "Verifying your login data..."
        );

        $success_login = $this->__(
            "You have successfully logged into eMailChef."
        );

        $error_login = $this->__(
            "Incorrect login credentials:"
        );

        $login = <<<EOF
<div id="emailchef_response_login">
            <div class="alert alert-info" id="login_emailchef_list_load">
                <span class="loading-spinner-emailchef"></span> $verify_login
            </div>
            <div class="alert alert-success" id="login_emailchef_list_success">
                $success_login
            </div>
            <div class="alert alert-danger" id="login_emailchef_list_danger">
                $error_login <span class="reason">{error}</span>
            </div>
        </div>
EOF;

        return $login;
    }

    /**
     * Return element html.
     *
     * @param Varien_Data_Form_Element_Abstract $element
     *
     * @return string
     */
    protected function _getElementHtml(
        Varien_Data_Form_Element_Abstract $element
    ) {
        return $this->_toHtml() . $this->login_emailchef();
    }

    /**
     * Generate button html.
     *
     * @return string
     */
    protected function _toHtml()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
                       ->setData(
                           array(
                               'id'    => 'emailchef_selftest_button',
                               'label' => __('Test Login'),
                           )
                       );

        return $button->toHtml();
    }
}
