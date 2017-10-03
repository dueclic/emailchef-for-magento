<?php

/**
 * Self-test button for connection details in system configuration.
 */
class Dueclic_Emailchef_Block_Adminhtml_System_Config_Form_Listselect
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    private function createListHtml()
    {
        ob_start();


        ?>
        <p><?php echo $this->__("Or"); ?> <a href="#"
                                             id="create_emailchef_list_trigger"><?php echo $this->__(
                    "create a new list"
                ); ?></a></p>
        <div id="create_emailchef_list">
            <p><strong><?php echo $this->__("Create new list"); ?></strong></p>
            <p><input type="text" class="input-text" id="new_list_name"
                      name="new_list_name"
                      placeholder="<?php echo $this->__("List name"); ?>"></p>
            <p class="note"><span><?php echo $this->__("List name"); ?></span>
            </p>
            <p><input type="text" class="input-text" id="new_list_description"
                      name="new_list_description"
                      placeholder="<?php echo $this->__(
                          "List description"
                      ); ?>"></p>
            <p class="note"><span><?php echo $this->__(
                        "List description"
                    ); ?></span></p>
            <p class="btn-emailchef">
                <button id="create_emailchef_list_btn"
                        title="<?php echo $this->__("Create list"); ?>"
                        type="button" class="scalable " onclick="" style="">
                    <span><span><span><?php echo $this->__(
                                    "Create list"
                                ); ?></span></span></span>
                </button>
            </p>
        </div>
        <div id="emailchef_response">
            <div class="alert alert-info" id="create_emailchef_list_load">
                <span class="loading-spinner-emailchef"></span> <?php echo __(
                    "Making a new list, please wait..."
                ); ?>
            </div>
            <div class="alert alert-success" id="create_emailchef_list_success">
                <?php echo __(
                    "Your list has been created. We’re now adding the custom fields."
                ); ?>
            </div>
            <div class="alert alert-danger" id="create_emailchef_list_danger">
                Errore nella creazione della lista indicata: <span
                        class="reason">{error}</span>
            </div>
            <div class="alert alert-info" id="create_emailchef_cf_load">
                <span class="loading-spinner-emailchef"></span> Creo i custom
                fields per la lista creata...
            </div>
            <div class="alert alert-success" id="create_emailchef_cf_success">
                Sistemazione dei custom fields per la lista avvenuta con
                successo.
            </div>
            <div class="alert alert-danger" id="create_emailchef_cf_danger">
                Errore nella sistemazione dei custom fields per la lista scelta:
                <span class="reason">{error}</span>
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
