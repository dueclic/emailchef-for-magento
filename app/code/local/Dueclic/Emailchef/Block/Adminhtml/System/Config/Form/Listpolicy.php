<?php

/**
 * Self-test button for connection details in system configuration.
 */
class Dueclic_Emailchef_Block_Adminhtml_System_Config_Form_Listpolicy
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    private function createListHtml()
    {
        ob_start();
        ?>
        <div id="emailchef_response_ccf">
            <div class="alert alert-info" id="create_emailchef_ccf_load">
                <span class="loading-spinner-emailchef"></span> Sistemo i custom fields per la lista appena scelta...
            </div>
            <div class="alert alert-success" id="create_emailchef_ccf_success">
                Sistemazione dei custom fields per la lista avvenuta con successo.
            </div>
            <div class="alert alert-danger" id="create_emailchef_ccf_danger">
                Errore nella sistemazione dei custom fields per la lista scelta: <span class="reason">{error}</span>
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
    protected function _getElementHtml($element)
    {
        return parent::_getElementHtml($element) . $this->createListHtml();
    }
}
