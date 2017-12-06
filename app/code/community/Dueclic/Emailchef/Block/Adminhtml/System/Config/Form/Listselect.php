<?php

/**
 * Self-test button for connection details in system configuration.
 */
class Dueclic_Emailchef_Block_Adminhtml_System_Config_Form_Listselect
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    private function createListHtml()
    {

        $or                     = $this->__("Or");
        $create_new_list        = $this->__(
            "create a new list"
        );
        $list_name              = $this->__("List name");
        $list_description       = $this->__(
            "List description"
        );
        $by_creating            = $this->__(
            "By creating a new list, you confirm its compliance with the privacy policy and the CAN-SPAM Act."
        );
        $create_list            = $this->__('Create list');
        $error_occurred         = $this->__("An error occurred while defining custom fields for this newly created list:");
        $custom_fields_created  = $this->__("Custom fields for this list have been successfully created.");
        $custom_fields_defining = $this->__("We're defining custom fields for this newly created list...");
        $error_creating_list    = $this->__("An error occurred while creating this list:");

        $list_created = $this->__(
            "Your list has been created. We’re now adding the custom fields."
        );

        $making_list = $this->__(
            "Making a new list, please wait..."
        );

        $undo = $this->__("Undo");

        $create = <<<EOF
<p>$or <a href="#" id="create_emailchef_list_trigger">$create_new_list</a></p>
        <div id="create_emailchef_list">
            <p><strong>$create_new_list</strong></p>
            <p><input type="text" class="input-text" id="new_list_name"
                      name="new_list_name"
                      placeholder="$list_name"></p>
            <p class="note"><span>$list_name</span>
            </p>
            <p><input type="text" class="input-text" id="new_list_description"
                      name="new_list_description"
                      placeholder="$list_description"></p>
            <p class="note"><span>$list_description</span></p>
            <p class="white" style="color:#fff;margin-top:10px;margin-bottom:10px;font-style:italic;">
                $by_creating
            </p>
            <p class="btn-emailchef">
                <button id="create_emailchef_list_btn"
                        title="$create_list"
                        type="button" class="scalable " onclick="" style="">
                        <span>$create_list</span>
                </button>

                <button id="undo_emailchef_list_btn"
                        title="$undo"
                        type="button" class="scalable" style="">
                    <span>$undo</span>
                </button>

            </p>
        </div>
        <div id="emailchef_response">
            <div class="alert alert-info" id="create_emailchef_list_load">
                <span class="loading-spinner-emailchef"></span> $making_list
            </div>
            <div class="alert alert-success" id="create_emailchef_list_success">
                $list_created
            </div>
            <div class="alert alert-danger" id="create_emailchef_list_danger">
                $error_creating_list <span class="reason">{error}</span>
            </div>
            <div class="alert alert-info" id="create_emailchef_cf_load">
                <span class="loading-spinner-emailchef"></span> $custom_fields_defining
            </div>
            <div class="alert alert-success" id="create_emailchef_cf_success">
                $custom_fields_created
            </div>
            <div class="alert alert-danger" id="create_emailchef_cf_danger">
                $error_occurred
                <span class="reason">{error}</span>
            </div>
        </div>
EOF;

        return $create;
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
