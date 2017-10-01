<?php

class Dueclic_Emailchef_OptinController extends Mage_Core_Controller_Front_Action {

	public function verifyAction() {

		$email = $this->getRequest()->getParam("email");

		/**
		 * @var $customer \Mage_Customer_Model_Customer
		 */

		$customer = Mage::getModel("customer/customer");
		$customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
		$customer->loadByEmail($email);

		if ($customer->getId()) {

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

			$upsert = $mgec->upsert_customer(
				$list_id,
				array(
					'first_name'  => $customer->getFirstname(),
					'last_name'   => $customer->getLastname(),
					'user_email'  => $email,
					'newsletter'  => 'yes',
					'customer_id' => $customer->getId()
				)
			);

			if ($upsert){
				Mage::getSingleton("core/session")->addSuccess("Iscrizione alla lista confermata con successo.");
				$this->_redirect("/");
				return;
			}
		}

		Mage::getSingleton("core/session")->addError("Iscrizione alla lista non confermata.");
		$this->_redirect("/");

	}

	public function unsubAction() {

		$email = $this->getRequest()->getParam("email");

		/**
		 * @var $customer \Mage_Customer_Model_Customer
		 */

		$customer = Mage::getModel("customer/customer");
		$customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
		$customer->loadByEmail($email);

		if ($customer->getId()) {

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

			$upsert = $mgec->upsert_customer(
				$list_id,
				array(
					'first_name'  => $customer->getFirstname(),
					'last_name'   => $customer->getLastname(),
					'user_email'  => $email,
					'newsletter'  => 'no',
					'customer_id' => $customer->getId()
				)
			);

			if ($upsert){
				Mage::getSingleton("core/session")->addSuccess("Disiscrizione alla lista confermata con successo.");
				$this->_redirect("/");
				return;
			}
		}

		Mage::getSingleton("core/session")->addError("Disiscrizione alla lista non confermata.");
		$this->_redirect("/");

	}

}