<?php

class Dueclic_Emailchef_CustomerController extends
    Mage_Core_Controller_Front_Action
{

    public function getCustomersAction()
    {

        $model = Mage::getModel("customer/customer");

        $customerCollection = $model->getCollection();
        $customersCollection = array();

        foreach ($customerCollection as $customerCollectionId) {

            if (is_object($customerCollectionId)) {
                $currentCustomerId = $customerCollectionId->getId();
            }

            if ( ! $currentCustomerId) {
                continue;
            }

            /**
             * @var $customer Mage_Customer_Model_Customer
             */

            $customer  = $model->load($currentCustomerId);
            $gender_id = $customer->getAttribute('gender')->getSource()
                ->getOptionId($customer->getGender());

            $customerAddressId = $customer->getDefaultBilling();

            /**
             * @var $gender Dueclic_Emailchef_Helper_Customer
             */

            $helper = Mage::helper("dueclic_emailchef/customer");

            $grand_total = $helper->getTotalOrdered($customer->getId());

            $data = array(
                "customer_id" => $customer->getId(),
                "customer_type" => "Customer",
                "firstname" => $customer->getFirstname(),
                "lastname"  => $customer->getLastname(),
                "email"     => $customer->getEmail(),
                "source"    => "eMailChef for Magento",
                "gender"    => $helper->getGenderStatus($gender_id),
                "birthday"  => $helper->getDateFromDateTime($customer->getDob()),
                "newsletter" => "no",
                "currency" => Mage::app()->getStore()->getCurrentCurrencyCode(),
            );

            $data = array_merge($data, $grand_total);

            if ($customerAddressId) {
                $address = Mage::getModel('customer/address')->load(
                    $customerAddressId
                );

                $data = array_merge(
                    $data, array(
                        "lang" => $helper->getStoreIdByCustomerCountryId($address->getCountry()),
                        "billing_company"   => $address->getData("company"),
                        "billing_address_1" => $address->getData('street'),
                        "billing_postcode"  => $address->getData("postcode"),
                        "billing_city"      => $address->getData("city"),
                        "billing_state"     => $address->getData("region"),
                        "billing_country" => $address->getCountry(),
                        "billing_phone" => $address->getData('telephone'),
                        "billing_phone_2" => $address->getData("fax"),

                    )
                );
            }

            $customersCollection[] = $data;

        }

        return $customersCollection;

    }

}