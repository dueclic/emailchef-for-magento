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
                <span class="loading-spinner-emailchef"></span> <?php echo $this->__("We're modifying custom fields for the chosen list..."); ?>
            </div>
            <div class="alert alert-success" id="create_emailchef_ccf_success">
        <?php echo $this->__("Custom fields for this list have been successfully modified."); ?>
            </div>
            <div class="alert alert-danger" id="create_emailchef_ccf_danger">
                <?php echo $this->__("An error occurred while modifying custom fields for the chosen list:"); ?> <span class="reason">{error}</span>
            </div>
        </div>
        <div id="emailchef_response_export">
            <div class="alert alert-info" id="create_emailchef_export_load">
                <span class="loading-spinner-emailchef"></span> <?php echo $this->__("We're executing first initial sync..."); ?>
            </div>
            <div class="alert alert-success" id="create_emailchef_export_success">
			    <?php echo $this->__("First customer sync has been successfully executed."); ?>
            </div>
            <div class="alert alert-danger" id="create_emailchef_export_danger">
			    <?php echo $this->__("An error occurred while executing first customer sync:"); ?> <span class="reason">{error}</span>
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