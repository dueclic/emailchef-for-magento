<?php

class Dueclic_Emailchef_Model_Observer {

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

			$newsletter = "no";

			$is_subscribed = Mage::app()->getFrontController()->getRequest()->getParam( 'is_subscribed' );

			if ( $is_subscribed !== null && $policy == "dopt" ) {
				$newsletter = "pending";
			}

			if ( $is_subscribed !== null && $policy == "sopt" ) {
				$newsletter = "yes";
			}

			/**
			 * @var $helper \Dueclic_Emailchef_Helper_Customer
			 * @var $customer \Mage_Customer_Model_Customer
			 */

			$helper   = Mage::helper( "dueclic_emailchef/customer" );
			$customer = $observer->getEvent()->getCustomer();

			$upsert = $mgec->upsert_customer( $list_id, $helper->getCustomerData(
				$customer->getId(),
				$newsletter
			) );

			if ( $upsert ) {
				Mage::log(
					sprintf(
						"Inserito nella lista %d il cliente %d (Nome: %s Cognome: %s Email: %s Consenso Newsletter: %s)",
						$list_id,
						$customer->getId(),
						$customer->getFirstname(),
						$customer->getLastname(),
						$customer->getEmail(),
						$newsletter
					),
					Zend_Log::INFO
				);
			} else {
				Mage::log(
					sprintf(
						"Inserimento nella lista %d del cliente %d (Nome: %s Cognome: %s Email: %s Consenso Newsletter: %s) non avvenuto",
						$list_id,
						$customer->getId(),
						$customer->getFirstname(),
						$customer->getLastname(),
						$customer->getEmail(),
						$newsletter
					),
					Zend_Log::ERR
				);
			}

			if ( $newsletter == "pending" ) {
				/**
				 * @var $emailTemplate \Dueclic_Emailchef_Model_Email
				 */

				$params = array(
					'shop_name'     => Mage::app()->getStore()->getName(),
					'customer_name' => $customer->getFirstname(),
					'verif_url'     => $config->getVerifyUrl(),
					'unsub_url'     => $config->getUnsubUrl(),
					'shop_logo'     => Mage::getSingleton( 'core/design_package' )->getSkinBaseUrl() . Mage::getStoreConfig( 'design/header/logo_src' )
				);

				$emailTemplate = Mage::getModel( 'dueclic_emailchef/email' );
				$emailTemplate->sendEmail(
					'emailchef_newsletter_dopt',
					"general",
					$customer->getEmail(),
					$customer->getFirstname(),
					'Conferma inserimento nella lista eMailChef',
					$params
				);

			}


		}

	}

}