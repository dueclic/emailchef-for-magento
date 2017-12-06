<?php

/**
 * Self-test button for connection details in system configuration.
 */
class Dueclic_Emailchef_Block_Adminhtml_System_Config_Form_Username
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    private function logo_emailchef()
    {

        $src = Mage::getSingleton('core/design_package')->getSkinBaseUrl().'/emailchef/img/emailchef.png';

        $logo = <<<EOF
<div class="emailchef-logo">
            <img src="$src">
        </div>
EOF;
        return $logo;

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
        $html = parent::_getElementHtml($element);
        return $this->logo_emailchef().$html;
    }
}
