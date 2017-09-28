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
            <p><input type="text" class="input-text" id="new_list_name" name="new_list_name"
                      placeholder="Nome lista"></p>
            <p class="note"><span>Nome lista</span></p>
            <p><input type="text" class="input-text" id="new_list_description" name="new_list_description"
                      placeholder="Descrizione lista"></p>
            <p class="note"><span>Descrizione lista</span></p>
            <p class="btn-emailchef">
                <button id="create_emailchef_list_btn" title="Crea lista"
                        type="button" class="scalable " onclick="" style="">
                    <span><span><span>Crea lista</span></span></span>
                </button>
            </p>
        </div>
        <div id="emailchef_response">
            <div class="alert alert-info" id="create_emailchef_list_load">
                <span class="loading-spinner-emailchef"></span> Creazione della lista in corso...
            </div>
            <div class="alert alert-success" id="create_emailchef_list_success">
                Lista creata con successo.
            </div>
            <div class="alert alert-danger" id="create_emailchef_list_danger">
                Errore nella creazione della lista indicata: <span class="reason">{error}</span>
            </div>
            <div class="alert alert-info" id="create_emailchef_cf_load">
                <span class="loading-spinner-emailchef"></span> Creo i custom fields per la lista creata...
            </div>
            <div class="alert alert-success" id="create_emailchef_cf_success">
                Sistemazione dei custom fields per la lista avvenuta con successo.
            </div>
            <div class="alert alert-danger" id="create_emailchef_cf_danger">
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
