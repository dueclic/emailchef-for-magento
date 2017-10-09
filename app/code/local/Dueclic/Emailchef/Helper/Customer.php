<?php

/**
 * Sample Widget Helper
 */
class Dueclic_Emailchef_Helper_Customer extends Mage_Core_Helper_Abstract
{

    /**
     * Format the Price.
     *
     * @param   float
     *
     * @return string
     */
    private static function _formatPrice($price)
    {
        return number_format($price, 2, '.', '');
    }

    public function getGenderStatus($gender)
    {
        if ($gender == 2) {
            return "f";
        }
        if ($gender == 1) {
            return "m";
        }

        return "na";
    }

    public function getStoreIdByCustomerCountryId($countryIdCustomer)
    {

	    $storeviews = array(
		    "website" => Mage::app()->getWebsite()->getName(),
		    "store" => Mage::app()->getStore()->getName(),
		    "view" => Mage::app()->getDefaultStoreView()->getName()
	    );

        $countryIdReturn   = null;
        $countryIdCustomer = trim((string)$countryIdCustomer);
        if ( ! strlen($countryIdCustomer)) {
            return $storeviews;
        }

        foreach (Mage::app()->getWebsites() as $website) {
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                foreach ($stores as $store) {
                    if ( ! $store->getIsActive()) {
                        continue;
                    }
                    foreach (
                        explode(',', $store->getConfig('general/country/allow'))
                        as $countryId
                    ) {
                        if (trim((string)$countryId) === $countryIdCustomer) {
                            $storeviews["view"] = $store->getName();
                            $storeviews["store"] = $group->getName();
                            $storeviews["website"] = $website->getName();
                            break 2;
                        }
                    }
                }
            }
        }

        return $storeviews;

    }

    /*public function getStoreIdByCustomerCountryId($countryIdCustomer)
    {
        $countryIdReturn   = null;
        $countryIdCustomer = trim((string)$countryIdCustomer);
        if ( ! strlen($countryIdCustomer)) {
            return false;
        }
        foreach (Mage::app()->getStores() as $store) {
            if ( ! $store->getIsActive()) {
                continue;
            }
            foreach (
                explode(',', $store->getConfig('general/country/allow'))
                as $countryId
            ) {
                if (trim((string)$countryId) === $countryIdCustomer) {
                    $countryIdReturn = $store->getName();
                    break 2;
                }
            }
        }

        return $countryIdReturn;
    }*/

    /**
     * Get Date from DateTime.
     *
     * @param type $datetime
     *
     * @return string
     */

    public function getDateFromDateTime($datetime)
    {
        if (empty($datetime)) {
            return '';
        }

        return date('Y-m-d', strtotime($datetime));
    }

    public function getTotalOrdered($customer_id, $withAdd=true)
    {

        $dateFormat      = 'm/d/y h:i:s';
        $lastDateTime    = date(
            $dateFormat,
            Mage::getModel('core/date')->timestamp(time()) - 7 * 3600 * 24
        );
        $thirtyDaysAgo   = date(
            $dateFormat,
            Mage::getModel('core/date')->timestamp(time()) - 30 * 3600 * 24
        );
        $twelveMonthsAgo = date(
            $dateFormat,
            Mage::getModel('core/date')->timestamp(time()) - 365 * 3600 * 24
        );

        $allOrdersTotalAmount     = 0;
        $allOrdersDateTimes       = array();
        $allOrdersStatuses        = array();
        $allOrdersTotals          = array();
        $allOrdersIds             = array();
        $allProductsIds           = array();
        $last30daysOrdersAmount   = 0;
        $last12monthsOrdersAmount = 0;
        $lastShipmentOrderId      = null;
        $lastShipmentOrderDate    = null;
        $lastShipmentOrderStatus  = null;
        $lastShipmentOrderIds     = null;
        $lastOrderDate            = null;
        $lastOrderIds             = array();

        $orders = Mage::getResourceModel('sales/order_collection')
            ->addAttributeToFilter('customer_id', $customer_id);

        foreach ($orders as $order) {

            $currentOrderTotal    = floatval($order->getGrandTotal());
            $allOrdersTotalAmount += $currentOrderTotal;

            $currentOrderCreationDate = $order->getCreatedAt();
            if ($currentOrderCreationDate > $thirtyDaysAgo) {
                $last30daysOrdersAmount += $currentOrderTotal;
            }
            if ($currentOrderCreationDate > $twelveMonthsAgo) {
                $last12monthsOrdersAmount += $currentOrderTotal;
            }

            $allOrdersStatuses[] = $order->getStatus();

            $currentOrderTotal                   = self::_formatPrice(
                $currentOrderTotal
            );
            $currentOrderId                      = $order->getIncrementId();
            $allOrdersTotals[$currentOrderId]    = $currentOrderTotal;
            $allOrdersDateTimes[$currentOrderId] = $currentOrderCreationDate;
            $allOrdersIds[$currentOrderId]       = $currentOrderId;

            if ($order->hasShipments() and ($order->getIncrementId()
                    > $lastShipmentOrderId)
            ) {
                $lastShipmentOrderId     = $order->getIncrementId();
                $lastShipmentOrderDate   = self::getDateFromDateTime(
                    $order->getCreatedAt()
                );
                $lastShipmentOrderStatus = $order->getStatus();
            }

            $items = $order->getAllItems();

            if ($lastOrderDate == null) {
                $lastOrderDate = $order->getCreatedAt();
            } else {
                if ($order->getCreatedAt() > $lastOrderDate) {
                    $lastOrderDate = $order->getCreatedAt();
                }
            }

            foreach ($items as $item) {
                if ( ! in_array($item->getProductId(), $allProductsIds)) {
                    $allProductsIds[] = $item->getProductId();
                }

                if (strtotime($order->getCreatedAt()) == strtotime(
                        $lastOrderDate
                    )
                ) {
                    if ( ! in_array($item->getProductId(), $lastOrderIds)) {
                        $lastOrderIds[] = $item->getProductId();
                    }
                }

            }

        }

        ksort($allOrdersDateTimes);
        ksort($allOrdersTotals);
        ksort($allOrdersIds);
        ksort($allOrdersStatuses);

        $latest_order_amount = end($allOrdersTotals);
        $latest_order_date   = self::getDateFromDateTime(
            end($allOrdersDateTimes)
        );

        /**
         * @var $order_status \Mage_Sales_Model_Order_Status
         */

        $order_status = Mage::getModel('sales/order_status');

        $latest_order_id     = end($allOrdersIds);
        $latest_order_status = $order_status->load(end($allOrdersStatuses))->getLabel();

        $report = array(
            "total_ordered_30d"           => self::_formatPrice(
                $last30daysOrdersAmount
            ),
            "total_ordered_12m"           => self::_formatPrice(
                $last12monthsOrdersAmount
            ),
            "all_ordered_product_ids"     => implode(", ", $allProductsIds),
            "total_orders"                => count($allOrdersIds),
            "total_ordered"               => self::_formatPrice(
                $allOrdersTotalAmount
            )
        );

        $additional = array(
            "latest_order_amount"         => $latest_order_amount,
            "latest_order_date"           => $latest_order_date,
            "latest_order_id"             => $latest_order_id,
            "latest_order_status"         => $latest_order_status,
            "latest_order_product_ids"    => implode(",", $lastOrderIds),
            "latest_shipped_order_id"     => $lastShipmentOrderId,
            "latest_shipped_order_date"   => $lastShipmentOrderDate,
            "latest_shipped_order_status" => $order_status->load($lastShipmentOrderStatus)->getLabel(),
        );

        if ($withAdd)
        	$report = array_merge($report, $additional);

        return $report;

    }

    /**
     * Get customer data
     *
     * @param $currentCustomerId
     * @param string $newsletter
     * @param array $filter
     * @return array|false
     */

    public function getCustomerData($currentCustomerId, $newsletter = "no", $storeIds = array())
    {

        $model = Mage::getModel("customer/customer");

        /**
         * @var $customer \Mage_Customer_Model_Customer
         */

        $customer  = $model->load($currentCustomerId);

        $gender_id = $customer->getAttribute('gender')->getSource()
            ->getOptionId($customer->getGender());

        $customerAddressId = $customer->getDefaultBilling();

        /**
         * @var $gender \Dueclic_Emailchef_Helper_Customer
         */

        $grand_total = $this->getTotalOrdered($customer->getId());

        if ($newsletter == "initial"){

            $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($customer->getEmail());
            if ($subscriber->getId()) {
                $newsletter = "yes";
            }
            else
                $newsletter = "no";

        }

        $data = array(
            "customer_id"   => $customer->getId(),
            "customer_type" => Mage::getModel('customer/group')->load(
                $customer->getGroupId()
            )->getCustomerGroupCode(),
            "first_name"    => $customer->getFirstname(),
            "last_name"     => $customer->getLastname(),
            "user_email"    => $customer->getEmail(),
            "source"        => "eMailChef for Magento",
            "gender"        => $this->getGenderStatus($gender_id),
            "birthday"      => $this->getDateFromDateTime($customer->getDob()),
            "newsletter"    => $newsletter,
            "currency"      => Mage::app()->getStore()->getCurrentCurrencyCode(
            ),
        );

        if ( $newsletter == "noinsert" )
            unset($data["newsletter"]);

        $data = array_merge($data, $grand_total);

        if (!empty($storeIds)){

            /**
             * @var $order \Mage_Sales_Model_Order
             */

            if (!empty($data["latest_order_id"])) {

	            $order = Mage::getModel('sales/order')->loadByIncrementId($data["latest_order_id"]);
	            $order_store_id = $order->getStore()->getId();

	            if (!in_array($order_store_id, $storeIds))
	                return false;

            }
            else
                return false;

        }

        if ($customerAddressId) {
            $address = Mage::getModel('customer/address')->load(
                $customerAddressId
            );

            $storeviews = $this->getStoreIdByCustomerCountryId(
                $address->getCountry()
            );

            $data = array_merge(
                $data, array(
                    "lang"              => $storeviews['view'],
                    "store_name"        => $storeviews["store"],
                    "website_name"      => $storeviews["website"],
                    "billing_company"   => $address->getData("company"),
                    "billing_address_1" => $address->getData('street'),
                    "billing_postcode"  => $address->getData("postcode"),
                    "billing_city"      => $address->getData("city"),
                    "billing_state"     => $address->getData("region"),
                    "billing_country"   => $address->getCountry(),
                    "billing_phone"     => $address->getData('telephone'),
                    "billing_phone_2"   => $address->getData("fax"),

                )
            );
        }

        return $data;

    }

    public function getCustomersData($action = "no", $storeIds = array())
    {
        $model = Mage::getModel("customer/customer");

        $customerCollection  = $model->getCollection();
        $customersCollection = array();

        foreach ($customerCollection as $customerCollectionId) {

            if (is_object($customerCollectionId)) {
                $currentCustomerId = $customerCollectionId->getId();
            }

            if ( ! $currentCustomerId) {
                continue;
            }

            $cdata = $this->getCustomerData($currentCustomerId, $action, $storeIds);

            if ($cdata !== false)
                $customersCollection[] = $cdata;

        }

        return $customersCollection;
    }

	/**
	 * @param $order \Mage_Sales_Model_Order
     * @return array
	 */

    public function getSyncOrderData($order){

	    $customerId = $order->getCustomerId();
	    $model = Mage::getModel("customer/customer");

	    /**
	     * @var $customer \Mage_Customer_Model_Customer
	     */

	    $customer  = $model->load($customerId);
	    $gender_id = $customer->getAttribute('gender')->getSource()
	                          ->getOptionId($customer->getGender());

	    $customerAddressId = $customer->getDefaultBilling();

	    /**
	     * @var $gender \Dueclic_Emailchef_Helper_Customer
	     */

	    $grand_total = $this->getTotalOrdered($customer->getId(), false);

	    if (!$order->getCustomerIsGuest()) {

		    $data = array(
			    "customer_id"   => $customer->getId(),
			    "customer_type" => Mage::getModel( 'customer/group' )->load(
				    $customer->getGroupId()
			    )->getCustomerGroupCode(),
			    "first_name"    => $customer->getFirstname(),
			    "last_name"     => $customer->getLastname(),
			    "user_email"    => $customer->getEmail(),
			    "source"        => "eMailChef for Magento",
			    "gender"        => $this->getGenderStatus( $gender_id ),
			    "birthday"      => $this->getDateFromDateTime( $customer->getDob() ),
			    "currency"      => $order->getOrderCurrencyCode(),
		    );

	    }
	    else {

		    $data = array(
			    "customer_id"   => $order->getCustomerId(),
			    "customer_type" => Mage::getModel( 'customer/group' )->load(
				    $order->getCustomerGroupId()
			    )->getCustomerGroupCode(),
			    "first_name"    => $order->getCustomerFirstname(),
			    "last_name"     => $order->getCustomerLastname(),
			    "user_email"    => $order->getCustomerEmail(),
			    "source"        => "eMailChef for Magento",
			    "gender"        => $this->getGenderStatus( $order->getCustomerGender() ),
			    "birthday"      => $this->getDateFromDateTime( $order->getCustomerDob() ),
			    "currency"      => $order->getOrderCurrencyCode(),
		    );
	    }

	    $data = array_merge($data, $grand_total);

	    $stores = array(
	        "lang" => $order->getStoreName(),
            "store_name" => $order->getStoreGroupName(),
            "website_name" => Mage::app()->getStore($order->getStoreId())->getWebsiteId()
        );

        $data = array_merge($data, $stores);

        /**
	     * @var $order_status \Mage_Sales_Model_Order_Status
	     */

	    $order_status = Mage::getModel('sales/order_status');

	    $latest_order = array(
	        'latest_order_id' => $order->getIncrementId(),
		    'latest_order_date' => $this->getDateFromDateTime($order->getUpdatedAt()),
		    'latest_order_amount' => self::_formatPrice($order->getGrandTotal()),
		    'latest_order_status' => $order_status->load($order->getStatus())->getLabel(),
	    );

	    $all_items_order = array();

	    foreach ($order->getAllItems() as $item) {
		    if ( ! in_array($item->getProductId(), $all_items_order)) {
			    $all_items_order[] = $item->getProductId();
		    }
	    }

	    $latest_order["latest_order_product_ids"] = implode(",", $all_items_order);

	    $data = array_merge($data, $latest_order);

	    $address = $order->getBillingAddress();

	    $order_address = array(
	        "lang" => $this->getStoreIdByCustomerCountryId(
		        $address->getCountry()
	        ),
	        "billing_company"   => $address->getData("company"),
	        "billing_address_1" => $address->getData('street'),
	        "billing_postcode"  => $address->getData("postcode"),
	        "billing_city"      => $address->getData("city"),
	        "billing_state"     => $address->getData("region"),
	        "billing_country"   => $address->getCountry(),
	        "billing_phone"     => $address->getData('telephone'),
	        "billing_phone_2"   => $address->getData("fax"),
	    );

	    $data = array_merge($data, $order_address);

	    $totals = array(
		    "total_ordered_30d"           => self::_formatPrice(
			    $latest_order["latest_order_amount"]
		    ),
		    "total_ordered_12m"           => self::_formatPrice(
			    $latest_order["latest_order_amount"]
		    ),
		    "all_ordered_product_ids"     => $latest_order["latest_order_product_ids"],
		    "total_orders"                => 1,
		    "total_ordered"               => self::_formatPrice(
			    $latest_order["latest_order_amount"]
		    )
	    );

	    if (!$order->getCustomerIsGuest()) {
	    	$totals = $this->getTotalOrdered($customerId, false);
	    }

	    $data = array_merge($data, $totals);

	    $data = array_merge($data, $this->flushAbandonedCarts());

	    if ($order->hasShipments()) {
		    $latest_shipped_order = array(
			    'latest_shipped_order_id' => $order->getIncrementId(),
			    'latest_shipped_order_date' => $this->getDateFromDateTime($order->getUpdatedAt()),
			    'latest_shipped_order_status' => $order_status->load($order->getStatus())->getLabel(),
		    );
		    $data = array_merge($data, $latest_shipped_order);
	    }

	    return $data;

    }

    public function flushAbandonedCarts(){
	    return array(
		    'ab_cart_prod_name_pr_hr'    => '',
		    'ab_cart_prod_desc_pr_hr'    => '',
		    'ab_cart_prod_pr_pr_hr'      => '',
		    'ab_cart_date'               => '',
		    'ab_cart_prod_id_pr_hr'      => '',
		    'ab_cart_prod_url_pr_hr'     => '',
		    'ab_cart_prod_url_img_pr_hr' => '',
		    'ab_cart_is_abandoned_cart'  => false,
	    );
    }

}