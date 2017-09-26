<?php

require_once(dirname(__DIR__) . "/lib/emailchef/class-emailchef.php");

class Dueclic_Emailchef_AjaxController extends Mage_Core_Controller_Front_Action
{

    public function checkCredentialsAction()
    {

        $response = array(
            "type" => "error",
            "msg"  => "I dati di accesso sono errati.",
        );

        $postData = $this->getRequest()->getPost();

        if (isset($postData['username']) && isset($postData['password'])) {
            $mgec = new MG_Emailchef($postData['username'], $postData['password']);

            if ($mgec->isLogged()) {
                $response["type"] = "success";
                $response["msg"] = "Utente loggato con successo.";
                $response["policy"] = $mgec->get_policy();
                $response["lists"] = $mgec->get_lists();
            }

        }

        $this->getResponse()->clearHeaders()->setHeader(
            'Content-Type', 'application/json', true
        );
        $this->getResponse()->setBody(
            json_encode($response)
        );
    }

}