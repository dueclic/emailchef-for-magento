<?php

/**
 * Self-test button for connection details in system configuration.
 */
class Dueclic_Emailchef_Block_Adminhtml_System_Config_Form_Listpolicy
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    private function createListHtml()
    {

        $modifying_cf =  $this->__("We're modifying custom fields for the chosen list...");
        $modified_cf = $this->__("Custom fields for this list have been successfully modified.");
        $error_cf = $this->__("An error occurred while modifying custom fields for the chosen list:");
        $progress_sync = $this->__("We're executing first initial sync...");
        $success_sync = $this->__("First customer sync has been successfully executed.");
        $error_sync = $this->__("An error occurred while executing first customer sync:");

        $html = <<<EOF
<div id="emailchef_response_ccf">
            <div class="alert alert-info" id="create_emailchef_ccf_load">
                <span class="loading-spinner-emailchef"></span> $modifying_cf
            </div>
            <div class="alert alert-success" id="create_emailchef_ccf_success"> 
                $modified_cf
            </div>
            <div class="alert alert-danger" id="create_emailchef_ccf_danger">
                 $error_cf <span class="reason">{error}</span>
            </div>
        </div>
        <div id="emailchef_response_export">
            <div class="alert alert-info" id="create_emailchef_export_load">
                <span class="loading-spinner-emailchef"></span> $progress_sync
            </div>
            <div class="alert alert-success" id="create_emailchef_export_success">
			    $success_sync
            </div>
            <div class="alert alert-danger" id="create_emailchef_export_danger">
			    $error_sync <span class="reason">{error}</span>
            </div>
        </div>
EOF;
        return $html;
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
