<?php

/**
 * Self-test button for connection details in system configuration.
 */
class Dueclic_Emailchef_Block_Adminhtml_System_Config_Form_Testbutton
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{

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
        return $this->_toHtml();
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
