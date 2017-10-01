<?php

class Dueclic_Emailchef_OptinController extends Mage_Core_Controller_Front_Action {

	public function verifyAction() {

		$this->loadLayout();

		$this->getLayout()
		     ->getBlock("head")
		     ->setTitle("Verifica sottoscrizione lista");

		$email = $this->getRequest()->getParam("email");



		$this->renderLayout();

	}

}