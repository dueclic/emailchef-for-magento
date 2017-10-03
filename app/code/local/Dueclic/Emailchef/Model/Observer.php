<?php

class Dueclic_Emailchef_Model_Observer {

    /**
     * @param $observer \Varien_Event_Observer
     */

    public function subscribedToNewsletter ($observer) {

        /**
         * @var $config \Dueclic_Emailchef_Model_Config
         */

        $config = Mage::getModel( "dueclic_emailchef/config" );

        $username = Mage::getStoreConfig( 'emailchef/general/username' );
        $password = Mage::getStoreConfig( 'emailchef/general/password' );
        $list_id  = Mage::getStoreConfig( 'emailchef/general/list' );
        $policy   = Mage::getStoreConfig( 'emailchef/general/policy' );

        $mgec = $config->getEmailChefInstance(
            $username, $password
        );

        if ( $mgec->isLogged() ) {

            $event      = $observer->getEvent();
            $subscriber = $event->getDataObject();
            $data       = $subscriber->getData();
            $request = Mage::app()->getRequest()->getParams();

            $email = $data['subscriber_email'];

            $statusChange = $subscriber->getIsStatusChanged();

            if ($statusChange == true) {

                if ($data['subscriber_status'] == "1") {

                    if ($policy == "dopt") {
                        $newsletter = "pending";
                    }

                    if ($policy == "sopt") {
                        $newsletter = "yes";
                    }

                }
                else {
                    $newsletter = "no";
                }

                $to_send = array(
                    "user_email" => $email,
                    "newsletter" => $newsletter
                );

                if (isset($request["firstname"]) && $request["lastname"]){
                    $to_send["first_name"] = $request["firstname"];
                    $to_send["last_name"] = $request["lastname"];
                }

                else {

                    /**
                     * @var $customer \Mage_Customer_Model_Customer
                     */

                    $customer = Mage::getModel("customer/customer");
                    $customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
                    $customer->loadByEmail($email);
                    $to_send["first_name"] = $customer->getFirstname();
                    $to_send["last_name"] = $customer->getLastname();

                }

                $upsert = $mgec->upsert_customer( $list_id, $to_send );

                if ( $upsert ) {
                    Mage::log(
                        sprintf(
                            "Consenso newsletter applicato al cliente %s su lista %d (Consenso: %s)",
                            $email,
                            $list_id,
                            $newsletter
                        ),
                        Zend_Log::INFO
                    );
                } else {
                    Mage::log(
                        sprintf(
                            "Consenso newsletter al cliente %s su lista %d (Consenso: %s) non avvenuto.",
                            $email,
                            $list_id,
                            $newsletter
                        ),
                        Zend_Log::ERR
                    );
                }


                if ($newsletter == "pending") {

                    /**
                     * @var $emailTemplate \Dueclic_Emailchef_Model_Email
                     */

                    $params = array(
                        'shop_name'     => Mage::app()->getStore()->getName(),
                        'customer_name' => $request["firstname"],
                        'verif_url'     => $config->getVerifyUrl(
                            $email
                        ),
                        'unsub_url'     => $config->getUnsubUrl(
                            $email
                        ),
                        'shop_logo'     => Mage::getSingleton(
                                'core/design_package'
                            )->getSkinBaseUrl() . Mage::getStoreConfig(
                                'design/header/logo_src'
                            )
                    );

                    $request["dest_info"] = $request["firstname"]." ".$request["lastname"];

                    $emailTemplate = Mage::getModel('dueclic_emailchef/email');
                    $emailTemplate->sendEmail(
                        'emailchef_newsletter_dopt',
                        "general",
                        $email,
                        $request["dest_info"],
                        'Conferma inserimento nella lista eMailChef',
                        $params
                    );

                    Mage::log(
                        sprintf(
                            "Double opt-in inviato al cliente %s su lista %d.",
                            $email,
                            $list_id
                        ),
                        Zend_Log::INFO
                    );

                }


            }
        }

    }

	/**
	 * @param $observer \Varien_Event_Observer
	 */

	public function prepareCustomerForDataSync( $observer ) {

		/**
		 * @var $config \Dueclic_Emailchef_Model_Config
		 */

		$config = Mage::getModel( "dueclic_emailchef/config" );

		$username = Mage::getStoreConfig( 'emailchef/general/username' );
		$password = Mage::getStoreConfig( 'emailchef/general/password' );
		$list_id  = Mage::getStoreConfig( 'emailchef/general/list' );
		$policy   = Mage::getStoreConfig( 'emailchef/general/policy' );

		$mgec = $config->getEmailChefInstance(
			$username, $password
		);

		if ( $mgec->isLogged() ) {

			/**
			 * @var $helper \Dueclic_Emailchef_Helper_Customer
			 * @var $customer \Mage_Customer_Model_Customer
			 */

			$helper   = Mage::helper( "dueclic_emailchef/customer" );
			$customer = $observer->getEvent()->getCustomer();

			$sync_data = $helper->getCustomerData(
				$customer->getId(),
				"noinsert"
			);

			$upsert = $mgec->upsert_customer( $list_id, $sync_data );

			if ( $upsert ) {
				Mage::log(
					sprintf(
						"Inserito nella lista %d il cliente %d (Nome: %s Cognome: %s Email: %s)",
						$list_id,
						$customer->getId(),
						$customer->getFirstname(),
						$customer->getLastname(),
						$customer->getEmail()
					),
					Zend_Log::INFO
				);
			} else {
				Mage::log(
					sprintf(
						"Inserimento nella lista %d del cliente %d (Nome: %s Cognome: %s Email: %s non avvenuto",
						$list_id,
						$customer->getId(),
						$customer->getFirstname(),
						$customer->getLastname(),
						$customer->getEmail()
					),
					Zend_Log::ERR
				);
			}

		}

	}

	public function checkAbandonedCarts ( $observer ){

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

        if (!$mgec->isLogged())
            return;

        $fromDate = date("Y-m-d H:i:s", strtotime('-7 days'));
        $toDate   = date("Y-m-d H:i:s", strtotime('-1 days'));

        /**
         * @var $quotes \Mage_Sales_Model_Resource_Quote_Collection
         */

        $quotes = Mage::getResourceModel('sales/quote_collection');

        $quotes->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('converted_at', array('null' => true))
            ->addFieldToFilter('customer_email', array('notnull' => true))
            ->addFieldToFilter('emailchef_sync', array('null' => true));

        /**
         * @var $quote \Mage_Sales_Model_Quote
         */

        foreach ($quotes as $quote) {

            if ($quote->getItemsQty() == 0)
                continue;

            if ($quote->getUpdatedAt() < $fromDate || $quote->getUpdatedAt() > $toDate ) {
                continue;
            }

            /**
             * @var $ab \Dueclic_Emailchef_Helper_Abandonedcart
             */

            $ab = Mage::helper("dueclic_emailchef/abandonedcart");
            $to_send = $ab->get($quote);
            $mgec->upsert_customer($list_id, $to_send);

            /**
             * @var $resource \Mage_Core_Model_Resource
             */

            $resource   = Mage::getSingleton('core/resource');

            $readConn   = $resource->getConnection('core_write');
            $sfq_table  = $resource->getTableName('sales_flat_quote');

            $readConn->update($sfq_table, array(
                "emailchef_sync" => 1,
                "is_active" => 0
            ), "customer_email = '".$to_send["user_email"]."'");

        }

    }

	public function prepareOrderForDataSync ( $observer ) {

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

		if ( $mgec->isLogged() ) {

			/**
			 * @var $helper \Dueclic_Emailchef_Helper_Customer
			 * @var $order \Mage_Sales_Model_Order
			 */

			$helper   = Mage::helper( "dueclic_emailchef/customer" );
			$order = $observer->getEvent()->getOrder();

			$syncOrderData = $helper->getSyncOrderData($order);
			$upsert = $mgec->upsert_customer( $list_id, $syncOrderData );

			if ( $upsert ) {
				Mage::log(
					sprintf(
						"Inserito nella lista %d i dati aggiornati del cliente %d (Nome: %s Cognome: %s e altri %d campi)",
						$list_id,
						$syncOrderData['customer_id'],
						$syncOrderData['first_name'],
						$syncOrderData['last_name'],
						intval( count( $syncOrderData ) - 2 )
					),
					Zend_Log::INFO
				);
			} else {
				$this->log(
					sprintf(
						"Inserimento nella lista %d dei dati aggiornati del cliente %d (Nome: %s Cognome: %s e altri %d campi) non avvenuto (Errore: %s)" ,
						$list_id,
						$syncOrderData['customer_id'],
						$syncOrderData['first_name'],
						$syncOrderData['last_name'],
						intval( count( $syncOrderData ) - 2 ),
						$mgec->lastError
					),
					Zend_Log::ERROR
				);
			}

		}

	}

}