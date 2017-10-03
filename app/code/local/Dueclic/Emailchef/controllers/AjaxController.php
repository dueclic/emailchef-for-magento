<?php

class Dueclic_Emailchef_AjaxController extends Mage_Core_Controller_Front_Action {



	public function initialSyncAction() {

		error_reporting( 0 );

		$args = $this->getRequest()->getPost();

		$this->getResponse()->clearHeaders()->setHeader(
			'Content-Type', 'application/json', true
		);

		$response = array(
			'type' => 'error',
			'msg'  => $this->__("eMailChef account credentials are wrong"),
		);

		/**
		 * @var $config \Dueclic_Emailchef_Model_Config
		 */

		$config = Mage::getModel( "dueclic_emailchef/config" );

		$username = Mage::getStoreConfig( 'emailchef/general/username' );
		$password = Mage::getStoreConfig( 'emailchef/general/password' );
		$list_id  = Mage::getStoreConfig( 'emailchef/general/list' );

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

		if ( $mgec->isLogged() ) {

			/**
			 * @var $helper \Dueclic_Emailchef_Helper_Customer
			 */

			$helper = Mage::helper( "dueclic_emailchef/customer" );

			$customers = $helper->getCustomersData("initial");

			foreach ( $customers as $customer ) {
				$mgec->upsert_customer(
					$list_id,
					$customer
				);
			}

			$response['type'] = "success";
			$response["msg"]  = $this->__( "Customers data sync was successfully sent.");

			Mage::getModel( 'core/config' )->saveConfig( 'emailchef/general/syncevent', 0 );

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
			json_encode( $response )
		);

	}

	public function createCustomFieldsAction() {

		$args = $this->getRequest()->getPost();

		$this->getResponse()->clearHeaders()->setHeader(
			'Content-Type', 'application/json', true
		);

		/**
		 * @var $config \Dueclic_Emailchef_Model_Config
		 */

		$config = Mage::getModel( "dueclic_emailchef/config" );

		if ( isset( $args['api_user'] ) && isset( $args['api_pass'] ) ) {

			$mgec = $config->getEmailChefInstance(
				$args['api_user'], $args['api_pass']
			);

		} else {

			$username = Mage::getStoreConfig( 'emailchef/general/username' );
			$password = Mage::getStoreConfig( 'emailchef/general/password' );

			$mgec = $config->getEmailChefInstance(
				$username, $password
			);
		}

		$response = array(
			'type' => 'error',
			'msg'  => $this->__("eMailChef account credentials are wrong"),
		);

		if ( $mgec->isLogged() ) {

			if ( ! $args['list_id'] || empty( $args['list_id'] ) ) {
				$response['msg'] = $this->__('Provided list is not valid.');

				$this->getResponse()->setBody(
					json_encode( $response )
				);

			}

			$init = $mgec->initialize_custom_fields( $args['list_id'] );

			if ( $init ) {

				$response['type'] = "success";
				$response['msg']  = $this->__('Custom fields for this list have been successfully created.');

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
			json_encode( $response )
		);

	}

	public function checkCredentialsAction() {

		$response = array(
			"type" => "error",
			"msg"  => $this->__("eMailChef account credentials are wrong."),
		);

		$args = $this->getRequest()->getPost();

		if ( isset( $args['username'] ) && isset( $args['password'] ) ) {

			/**
			 * @var $config \Dueclic_Emailchef_Model_Config
			 */

			$config = Mage::getModel( "dueclic_emailchef/config" );

			$mgec = $config->getEmailChefInstance(
				$args['username'], $args['password']
			);

			if ( $mgec->isLogged() ) {

                /**
                 * @var $resource \Mage_Core_Model_Resource
                 */

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
				$response["lists"]  = $mgec->get_lists();
			}

		}

		$this->getResponse()->clearHeaders()->setHeader(
			'Content-Type', 'application/json', true
		);
		$this->getResponse()->setBody(
			json_encode( $response )
		);
	}

	public function addListAction() {

		error_reporting( 0 );

		$args = $this->getRequest()->getPost();

		$this->getResponse()->clearHeaders()->setHeader(
			'Content-Type', 'application/json', true
		);

		/**
		 * @var $config \Dueclic_Emailchef_Model_Config
		 */

		$config = Mage::getModel( "dueclic_emailchef/config" );

		if ( isset( $args['api_user'] ) && isset( $args['api_pass'] ) ) {

			$mgec = $config->getEmailChefInstance(
				$args['api_user'], $args['api_pass']
			);

		} else {

			$username = Mage::getStoreConfig( 'emailchef/general/username' );
			$password = Mage::getStoreConfig( 'emailchef/general/password' );

			$mgec = $config->getEmailChefInstance(
				$username, $password
			);
		}

		$response = array(
			'type' => 'error',
			'msg'  => $this->__("eMailChef account credentials are wrong"),
		);

		if ( $mgec->isLogged() ) {

			if ( ! isset($args['list_name']) || empty( $args['list_name'] ) ) {
				$response['msg']
					= $this->__("Provide a valid name for eMailChef list.,fornisci un nome valido per la lista eMailChef");
				$this->getResponse()->setBody(
					json_encode( $response )
				);
			}

			if ( ! $args['list_desc'] || empty( $args['list_desc'] ) ) {
				$args['list_desc'] = "";
			}

			$list_id = $mgec->create_list(
				$args['list_name'], $args['list_desc']
			);

			$response['full_response'] = $mgec->lastResponse;

			if ( $list_id !== false ) {

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
					json_encode( $response )
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
			json_encode( $response )
		);

	}

}