<?php

class Dueclic_Emailchef_AjaxController extends Mage_Core_Controller_Front_Action
{

    public function initialSyncAction()
    {

        error_reporting(0);
        set_time_limit(0);
        ini_set('mysql.connect_timeout', '0');
        ini_set('max_execution_time', '0');

        $args = $this->getRequest()->getPost();

        $this->getResponse()->clearHeaders()->setHeader(
            'Content-Type', 'application/json', true
        );

        $response = array(
            'type' => 'error',
            'msg'  => $this->__("eMailChef account credentials are wrong."),
        );

        /**
         * @var $config \Dueclic_Emailchef_Model_Config
         */

        $config = Mage::getModel("dueclic_emailchef/config");

        $username = $args['username'];
        $password = $args['password'];
        $list_id  = $args["list_id"];

        $mgec = $config->getEmailChefInstance(
            $username, $password
        );

        Mage::log(
            sprintf(
                'Avviata sincronizzazione iniziale per la lista %d',
                $list_id
            ),
            Zend_Log::INFO
        );

        if ($mgec->isLogged()) {

            if ($args["store"] != "default") {
                $what = explode("_", $args["store"]);

                if ($what[0] == "website") {
                    $storeIds = Mage::app()->getWebsite($what[1])->getStoreIds();
                }
                if ($what[0] == "store") {
                    $storeIds[] = Mage::app()->getStore($what[1])->getId();
                }

            }

            /**
             * @var $config \Mage_Core_Model_Config
             */

            $config = Mage::getConfig();

            $scope   = "default";
            $storeId = 0;

            if ( ! empty($storeIds)) {

                if (count($storeIds) == 1) {
                    $scope   = "stores";
                    $storeId = $storeIds[0];
                } else {
                    $scope   = "websites";
                    $storeId = Mage::app()->getWebsite($what[1])->getId();
                }

            } else {
                $storeIds = array();
                foreach (Mage::app()->getWebsites() as $website) {
                    foreach ($website->getGroups() as $group) {
                        $stores = $group->getStores();
                        foreach ($stores as $store) {
                            $storeIds[] = $store->getId();
                        }
                    }
                }
            }

            $config->saveConfig('emailchef/general/syncevent', 0, $scope, $storeId);

            /**
             * @var $helper \Dueclic_Emailchef_Helper_Customer
             */

            $helper = Mage::helper("dueclic_emailchef/customer");

            if ($args["store"] != "default" && $what[0] == "website") {
                $website_id = Mage::app()->getWebsite($what[1])->getID();
                $customers  = $helper->getCustomersByWebsiteId($website_id);
            } else {
                $customers = $helper->getCustomersData("initial", $storeIds);
            }

	        $customers_import = array();

	        foreach ($customers as $customer) {

		        $curCustomer = array();

		        foreach ( $customer as $placeholder => $value ) {

			        if ( $placeholder == "user_email" ) {
				        $placeholder = "email";
			        }

			        $curCustomer[] = array(
				        "placeholder" => $placeholder,
				        "value"       => $value
			        );
		        }

		        if (count($customers_import) > 100) {
			        $mgec->import($list_id, $customers_import);
			        $customers_import = array();
			        $customers_import[] = $curCustomer;
		        }
		        else {
			        $customers_import[] = $curCustomer;
		        }

	        }

	        if (count($customers_import) > 0)
		        $mgec->import($list_id, $customers_import);

	        $response['type'] = "success";
            $response["msg"]  = $this->__("Customers data sync was successfully sent.");

            Mage::log(
                sprintf(
                    'Esportazione per la lista %d avvenuta con successo.'
                    ,
                    $list_id
                ),
                Zend_Log::INFO
            );

        } else {

            Mage::log(
                sprintf(
                    'Esportazione per la lista %d non avvenuta. Motivo errore: %s',
                    $list_id,
                    $response['msg']
                ),
                Zend_Log::ERR
            );

        }

        $this->getResponse()->setBody(
            json_encode($response)
        );

    }

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
            'msg'  => $this->__("eMailChef account credentials are wrong"),
        );

        if ($mgec->isLogged()) {

            if ( ! $args['list_id'] || empty($args['list_id'])) {
                $response['msg'] = $this->__('Provided list is not valid.');

                $this->getResponse()->setBody(
                    json_encode($response)
                );

            }

            $mgec->upsert_integration($args['list_id']);

            $init = $mgec->initialize_custom_fields($args['list_id']);

            if ($init) {

                $response['type'] = "success";
                $response['msg']  = $this->__('Custom fields for this list have been successfully created.');
                $response['temp'] = $mgec->get_temp_custom_fields();

                Mage::log(
                    sprintf(
                        'Creati custom fields per la lista %d',
                        $args['list_id']
                    ),
                    Zend_Log::INFO
                );

            }

            $response['msg'] = $mgec->lastError;

            Mage::log(
                sprintf(
                    'Tentativo fallito di creazione dei custom fields per la lista %d',
                    $args['list_id']
                ),
                Zend_Log::ERR
            );

        }

        $this->getResponse()->setBody(
            json_encode($response)
        );

    }

    public function checkCredentialsAction()
    {

        $response = array(
            "type" => "error",
            "msg"  => $this->__("eMailChef account credentials are wrong."),
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

                /**
                 * @var $resource \Mage_Core_Model_Resource
                 */

                $lists = $mgec->get_lists();

                $resource = Mage::getSingleton("core/resource");

                if ( ! $resource->getConnection('core_read')->tableColumnExists(
                    $resource->getTableName('sales_flat_quote'), 'emailchef_sync'
                )
                ) {
                    $resource->getConnection('core_write')->addColumn(
                        $resource->getTableName('sales_flat_quote'), 'emailchef_sync',
                        "INT( 1 ) NULL"
                    );
                }

                $response["class"]  = get_class($resource);
                $response["type"]   = "success";
                $response["msg"]    = $this->__("User logged successfully.");
                $response["policy"] = $mgec->get_policy();
                $response["lists"]  = $lists;
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
            'msg'  => $this->__("eMailChef account credentials are wrong."),
        );

        if ($mgec->isLogged()) {

            if ( ! isset($args['list_name']) || empty($args['list_name'])) {
                $response['msg']
                    = $this->__("Provide a valid name for eMailChef list.");
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
                $response['msg']     = $this->__("List has been created.");
                $response['list_id'] = $list_id;

                Mage::log(
                    sprintf(
                        'Creata lista %d (Nome: %s, Descrizione: %s)',
                        $list_id,
                        $args['list_name'],
                        $args['list_desc']
                    ),
                    Zend_Log::INFO
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
                Zend_Log::ERR
            );

        }

        $this->getResponse()->setBody(
            json_encode($response)
        );

    }

    public function isActiveAction(){

        $this->getResponse()->clearHeaders()->setHeader(
            'Content-Type', 'application/json', true
        );

        $list_ids = array();

        foreach (Mage::app()->getWebsites() as $website) {
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                foreach ($stores as $store) {
                    $list_ids[] = Mage::getStoreConfig('emailchef/general/list', $store->getId());
                }
            }
        }

        $this->getResponse()->setBody(
            json_encode(array(
                "is_active" => true,
                "list_ids" => $list_ids
            ))
        );

    }

}