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
        <p>Oppure <a href="#" id="create_emailchef_list_trigger">crea una nuova lista</a></p>
        <div id="create_emailchef_list">
            <p><strong>Crea una nuova lista</strong></p>
            <p><input type="text" class="input-text" name="new_list_name"
                      placeholder="Nome lista"></p>
            <p class="note"><span>Nome lista</span></p>
            <p><input type="text" class="input-text" name="new_list_description"
                      placeholder="Descrizione lista"></p>
            <p class="note"><span>Descrizione lista</span></p>
            <p class="btn-emailchef">
                <button id="create_emailchef_list_btn" title="Crea lista"
                        type="button" class="scalable " onclick="" style="">
                    <span><span><span>Crea lista</span></span></span>
                </button>
            </p>
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
