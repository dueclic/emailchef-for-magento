<?php

class Dueclic_Emailchef_AjaxController extends Mage_Core_Controller_Front_Action
{

    public function createCustomFieldsAction()
    {

        $args = $this->getRequest()->getPost();

        $this->getResponse()->clearHeaders()->setHeader(
            'Content-Type', 'application/json', true
        );

        /**
         * @var $config \Dueclic_Emailchef_Model_Config
         */

        $config = Mage::getModel("dueclic_emailchef/config");

        if (isset($args['api_user']) && isset($args['api_pass'])) {

            $mgec = $config->getEmailChefInstance(
                $args['api_user'], $args['api_pass']
            );

        } else {

            $username = Mage::getStoreConfig('emailchef/general/username');
            $password = Mage::getStoreConfig('emailchef/general/password');

            $mgec = $config->getEmailChefInstance(
                $username, $password
            );
        }

        $response = array(
            'type' => 'error',
            'msg'  => 'Username o password non corretti.',
        );

        if ($mgec->isLogged()) {

            if ( ! $args['list_id'] || empty($args['list_id'])) {
                $response['msg'] = 'Lista assegnata non valida.';

                $this->getResponse()->setBody(
                    json_encode($response)
                );

            }

            $init = $mgec->initialize_custom_fields($args['list_id']);

            if ($init) {

                $response['type'] = "success";
                $response['msg']  = "Custom fields creati con successo.";

                Mage::log(
                    sprintf(
                        'Creati custom fields per la lista %d',
                        $args['list_id']
                    ),
                    8
                );

                $this->getResponse()->setBody(
                    json_encode($response)
                );

            }

            $response['msg'] = $mgec->lastError;

            Mage::log(
                sprintf(
                    'Tentativo fallito di creazione dei custom fields per la lista %d',
                    $args['list_id']
                ),
                3
            );


        }

    }

    public function checkCredentialsAction()
    {

        $response = array(
            "type" => "error",
            "msg"  => "I dati di accesso sono errati.",
        );

        $args = $this->getRequest()->getPost();

        if (isset($args['username']) && isset($args['password'])) {

            /**
             * @var $config \Dueclic_Emailchef_Model_Config
             */

            $config = Mage::getModel("dueclic_emailchef/config");

            $mgec = $config->getEmailChefInstance(
                $args['username'], $args['password']
            );

            if ($mgec->isLogged()) {
                $response["type"]   = "success";
                $response["msg"]    = "Utente loggato con successo.";
                $response["policy"] = $mgec->get_policy();
                $response["lists"]  = $mgec->get_lists();
            }

        }

        $this->getResponse()->clearHeaders()->setHeader(
            'Content-Type', 'application/json', true
        );
        $this->getResponse()->setBody(
            json_encode($response)
        );
    }

    public function addListAction()
    {

        error_reporting(0);

        $args = $this->getRequest()->getPost();

        $this->getResponse()->clearHeaders()->setHeader(
            'Content-Type', 'application/json', true
        );

        /**
         * @var $config \Dueclic_Emailchef_Model_Config
         */

        $config = Mage::getModel("dueclic_emailchef/config");

        if (isset($args['api_user']) && isset($args['api_pass'])) {

            $mgec = $config->getEmailChefInstance(
                $args['api_user'], $args['api_pass']
            );

        } else {

            $username = Mage::getStoreConfig('emailchef/general/username');
            $password = Mage::getStoreConfig('emailchef/general/password');

            $mgec = $config->getEmailChefInstance(
                $username, $password
            );
        }

        $response = array(
            'type' => 'error',
            'msg'  => 'Username o password non corretti.',
        );

        if ($mgec->isLogged()) {

            if ( ! $args['list_name'] || empty($args['list_name'])) {
                $response['msg']
                    = 'Inserisci un nome e una descrizione per la nuova lista';
                $this->getResponse()->setBody(
                    json_encode($response)
                );
            }

            if ( ! $args['list_desc'] || empty($args['list_desc'])) {
                $args['list_desc'] = "";
            }

            $list_id = $mgec->create_list(
                $args['list_name'], $args['list_desc']
            );

            $response['full_response'] = $mgec->lastResponse;

            if ($list_id !== false) {

                $response['type']    = "success";
                $response['msg']     = "Lista creata con successo.";
                $response['list_id'] = $list_id;

                Mage::log(
                    sprintf(
                        'Creata lista %d (Nome: %s, Descrizione: %s)',
                        $list_id,
                        $args['list_name'],
                        $args['list_desc']
                    ),
                    6
                );

                $this->getResponse()->setBody(
                    json_encode($response)
                );

            }

            $response['msg'] = $mgec->lastError;

            Mage::log(
                sprintf(
                    'Tentativo fallito di creazione della lista %d (Nome: %s, Descrizione: %s)',
                    $list_id,
                    $args['list_name'],
                    $args['list_desc']
                ),
                3
            );

        }

        $this->getResponse()->setBody(
            json_encode($response)
        );

    }

}