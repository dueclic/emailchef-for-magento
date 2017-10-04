<?php

/**
 * Self-test button for connection details in system configuration.
 */
class Dueclic_Emailchef_Block_Adminhtml_System_Config_Form_Testbutton
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    private function login_emailchef()
    {
        ob_start();

        ?>

        <div id="emailchef_response_login">
            <div class="alert alert-info" id="login_emailchef_list_load">
                <span class="loading-spinner-emailchef"></span> <?php echo $this->__(
                    "Verifying your login data..."
                ); ?>
            </div>
            <div class="alert alert-success" id="login_emailchef_list_success">
                <?php echo $this->__(
                    "You have successfully logged into eMailChef."
                ); ?>
            </div>
            <div class="alert alert-danger" id="login_emailchef_list_danger">
                <?php echo $this->__("Incorrect login credentials:"); ?> <span
                    class="reason">{error}</span>
            </div>
        </div>
        <?php
        return ob_get_clean();
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
                    'id' => 'emailchef_selftest_button',
                    'label' => __('Test Login'),
                )
            );

        return $button->toHtml();
    }
}
