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

    public function getTotalOrdered($customer_id, $withAdd = true)
    {

        $dateFormat = 'm/d/y h:i:s';
        $lastDateTime = date(
            $dateFormat,
            Mage::getModel('core/date')->timestamp(time()) - 7 * 3600 * 24
        );
        $thirtyDaysAgo = date(
            $dateFormat,
            Mage::getModel('core/date')->timestamp(time()) - 30 * 3600 * 24
        );
        $twelveMonthsAgo = date(
            $dateFormat,
            Mage::getModel('core/date')->timestamp(time()) - 365 * 3600 * 24
        );

        $allOrdersTotalAmount = 0;
        $allOrdersDateTimes = array();
        $allOrdersStatuses = array();
        $allOrdersTotals = array();
        $allOrdersIds = array();
        $allProductsIds = array();
        $last30daysOrdersAmount = 0;
        $last12monthsOrdersAmount = 0;
        $lastShipmentOrderId = null;
        $lastShipmentOrderDate = null;
        $lastShipmentOrderStatus = null;
        $lastShipmentOrderIds = null;
        $lastOrderDate = null;
        $lastOrderIds = array();

        $orders = Mage::getResourceModel('sales/order_collection')
            ->addAttributeToFilter('customer_id', $customer_id);

        /**
         * @var $order \Mage_Sales_Model_Order
         */

        foreach ($orders as $order) {

            $currentOrderTotal = floatval($order->getGrandTotal());
            $currentOrderCreationDate = $order->getCreatedAt();

            if (!$order->isCanceled()) {
                $allOrdersTotalAmount += $currentOrderTotal;

                if (strtotime($currentOrderCreationDate) > strtotime($thirtyDaysAgo)) {
                    $last30daysOrdersAmount += $currentOrderTotal;
                }
                if (strtotime($currentOrderCreationDate) > strtotime($twelveMonthsAgo)) {
                    $last12monthsOrdersAmount += $currentOrderTotal;
                }
            }

            $allOrdersStatuses[] = $order->getStatus();

            $currentOrderTotal = self::_formatPrice(
                $currentOrderTotal
            );
            $currentOrderId = $order->getIncrementId();
            $allOrdersTotals[$currentOrderId] = $currentOrderTotal;
            $allOrdersDateTimes[$currentOrderId] = $currentOrderCreationDate;
            $allOrdersIds[$currentOrderId] = $currentOrderId;

            if ($order->hasShipments() and ($order->getCreatedAt()
                    > $lastShipmentOrderDate)
            ) {
                $lastShipmentOrderId = $order->getIncrementId();
                $lastShipmentOrderDate = self::getDateFromDateTime(
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
                if (!in_array($item->getProductId(), $allProductsIds)) {
                    $allProductsIds[] = $item->getProductId();
                }

            }

        }

        ksort($allOrdersDateTimes);
        ksort($allOrdersTotals);
        ksort($allOrdersIds);
        ksort($allOrdersStatuses);

        $latest_order_amount = end($allOrdersTotals);
        $latest_order_date = self::getDateFromDateTime(
            end($allOrdersDateTimes)
        );

        /**
         * @var $order_status \Mage_Sales_Model_Order_Status
         */

        $order_status = Mage::getModel('sales/order_status');

        $latest_order_id = end($allOrdersIds);

        /**
         * @var $latest_order \Mage_Sales_Model_Order
         */

        $latest_order = Mage::getModel('sales/order')->loadByIncrementId($latest_order_id);

        $items = $latest_order->getAllItems();

        foreach ($items as $item) {
            $lastOrderIds[] = $item->getProductId();
        }

        $latest_order_status = $order_status->load($latest_order->getStatus())->getLabel();

        $report = array(
            "total_ordered_30d" => self::_formatPrice(
                $last30daysOrdersAmount
            ),
            "total_ordered_12m" => self::_formatPrice(
                $last12monthsOrdersAmount
            ),
            "all_ordered_product_ids" => implode(", ", $allProductsIds),
            "total_orders" => count($allOrdersIds),
            "total_ordered" => self::_formatPrice(
                $allOrdersTotalAmount
            )
        );

        $additional = array(
            "latest_order_amount" => $latest_order_amount,
            "latest_order_date" => $latest_order_date,
            "latest_order_id" => $latest_order_id,
            "latest_order_status" => $latest_order_status,
            "latest_order_product_ids" => implode(", ", $lastOrderIds),
            "latest_shipped_order_id" => $lastShipmentOrderId,
            "latest_shipped_order_date" => $lastShipmentOrderDate,
            "latest_shipped_order_status" => $order_status->load($lastShipmentOrderStatus)->getLabel(),
        );

        if ($withAdd)
            $report = array_merge($report, $additional);

        return $report;

    }

    public function getCustomerSyncData(\Mage_Customer_Model_Customer $customer)
    {
        $gender_id = $customer->getAttribute('gender')->getSource()
            ->getOptionId($customer->getGender());

        $customerAddressId = $customer->getDefaultBilling();

        /**
         * @var $gender \Dueclic_Emailchef_Helper_Customer
         * @var $subscriber \Mage_Newsletter_Model_Subscriber
         */

        $grand_total = $this->getTotalOrdered($customer->getId());

        $subscriber = Mage::getModel('newsletter/subscriber');
        $subscriber = $subscriber->loadByEmail($customer->getEmail());

        if ($subscriber->getId()) {
            $newsletter = "yes";
        } else
            $newsletter = "no";

        $data = array(
            "customer_id" => $customer->getId(),
            "customer_type" => Mage::getModel('customer/group')->load(
                $customer->getGroupId()
            )->getCustomerGroupCode(),
            "customer_created" => date("Y-m-d", $customer->getCreatedAtTimestamp()),
            "first_name" => $customer->getFirstname(),
            "last_name" => $customer->getLastname(),
            "user_email" => $customer->getEmail(),
            "source" => "eMailChef for Magento",
            "gender" => $this->getGenderStatus($gender_id),
            "birthday" => $this->getDateFromDateTime($customer->getDob()),
            "newsletter" => $newsletter,
            "currency" => Mage::app()->getStore()->getCurrentCurrencyCode(),
        );

        $data = array_merge($data, $grand_total);

        if ($customerAddressId) {

            /**
             * @var $address \Mage_Customer_Model_Address
             */

            $address = Mage::getModel('customer/address')->load(
                $customerAddressId
            );

            $data = array_merge(
                $data, array(
                    "billing_company" => $address->getData("company"),
                    "billing_address_1" => $address->getData('street'),
                    "billing_postcode" => $address->getData("postcode"),
                    "billing_city" => $address->getData("city"),
                    "billing_state" => $address->getData("region"),
                    "billing_country" => $address->getCountry(),
                    "billing_phone" => $address->getData('telephone'),
                    "billing_phone_2" => $address->getData("fax"),

                )
            );
        }

        $data = array_merge($data, array(
            "lang" => Mage::app()->getStore($customer->getData("store_id"))->getName(),
            "store_name" => Mage::app()->getStore($customer->getData("store_id"))->getGroup()->getName(),
            "website_name" => Mage::app()->getStore($customer->getData("store_id"))->getWebsite()->getName(),
        ));

        return $data;
    }


    /**
     * Get customer data by website ID
     *
     * @param int $website_id
     * @return array
     *
     */

    public function getCustomersByWebsiteId($website_id)
    {
        /**
         * @var $model \Mage_Customer_Model_Customer
         */

        $model = Mage::getModel("customer/customer");

        /**
         * @var $customerCollection \Mage_Customer_Model_Resource_Customer_Collection
         */

        $customerCollection = $model->getCollection()->addAttributeToFilter("website_id", $website_id);
        $customersCollection = array();

        foreach ($customerCollection as $customerCollectionId) {

            if (is_object($customerCollectionId)) {
                $currentCustomerId = $customerCollectionId->getId();
            }

            if (!$currentCustomerId) {
                continue;
            }

            $cdata = $this->getCustomerSyncData($model->load($currentCustomerId));

            if ($cdata !== false)
                $customersCollection[] = $cdata;

        }

        return $customersCollection;
    }

    /**
     * Get customer data
     *
     * @param $currentCustomerId
     * @param string $newsletter
     * @param array $storeIds
     * @param array $stores
     *
     * @return array|false
     */

    public function getCustomerData($currentCustomerId, $newsletter = "no", $storeIds = array(), $stores = array())
    {

        $model = Mage::getModel("customer/customer");

        /**
         * @var $customer \Mage_Customer_Model_Customer
         */

        $customer = $model->load($currentCustomerId);

        $gender_id = $customer->getAttribute('gender')->getSource()
            ->getOptionId($customer->getGender());

        $customerAddressId = $customer->getDefaultBilling();

        /**
         * @var $gender \Dueclic_Emailchef_Helper_Customer
         */

        $grand_total = $this->getTotalOrdered($customer->getId());

        if ($newsletter == "initial") {

            $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($customer->getEmail());
            if ($subscriber->getId()) {
                $newsletter = "yes";
            } else
                $newsletter = "no";

        }

        $data = array(
            "customer_id" => $customer->getId(),
            "customer_type" => Mage::getModel('customer/group')->load(
                $customer->getGroupId()
            )->getCustomerGroupCode(),
            "customer_created" => date("Y-m-d", $customer->getCreatedAtTimestamp()),
            "first_name" => $customer->getFirstname(),
            "last_name" => $customer->getLastname(),
            "user_email" => $customer->getEmail(),
            "source" => "eMailChef for Magento",
            "gender" => $this->getGenderStatus($gender_id),
            "birthday" => $this->getDateFromDateTime($customer->getDob()),
            "newsletter" => $newsletter,
            "currency" => Mage::app()->getStore()->getCurrentCurrencyCode(),
        );

        if ($newsletter == "noinsert")
            unset($data["newsletter"]);

        $data = array_merge($data, $grand_total);

        if (!empty($storeIds)) {
            
            /**
             * @var $order \Mage_Sales_Model_Order
             */

            $website_id = $customer->getData("website_id");
            $store_id = $customer->getData("store_id");

            /**
             * If website_id = 0 and store_id = 0
             * Customer is created by admin and store_view is admin
             */

            $isFound = false;

            if ($store_id != 0) {
                if (in_array($store_id, $storeIds)) {
                    $data = array_merge(
                        $data, array(
                            "lang" => Mage::app()->getStore($store_id)->getName(),
                            "store_name" => Mage::app()->getStore($store_id)->getGroup()->getName(),
                            "website_name" => Mage::app()->getStore($store_id)->getWebsite()->getName(),
                        )
                    );
                    $isFound = true;
                }
            } else {

                if (!empty($data["latest_order_id"])) {

                    $order = Mage::getModel('sales/order')->loadByIncrementId($data["latest_order_id"]);
                    $order_store_id = $order->getStore()->getId();

                    if (in_array($order_store_id, $storeIds)) {
                        $data = array_merge(
                            $data, array(
                                "lang" => Mage::app()->getStore($order->getStoreId())->getName(),
                                "store_name" => $order->getStoreGroupName(),
                                "website_name" => Mage::app()->getStore($order->getStoreId())->getWebsite()->getName(),
                            )
                        );
                        $isFound = true;
                    }

                } else {

                    if ($customer->getData("website_id") == $website_id) {

                        $data = array_merge(
                            $data, array(
                                "lang" => Mage::app()->getStore($store_id)->getName(),
                                "store_name" => Mage::app()->getStore($store_id)->getGroup()->getName(),
                                "website_name" => Mage::app()->getWebsite($website_id)->getName(),
                            )
                        );

                        $isFound = true;
                    }

                }

            }

            if ($data["lang"] == "Admin" && $data["store_name"] == "Default" && $website_id === NULL)
                $data["website_name"] = "";

            /*if (!$isFound) {

                if (!empty($data["latest_order_id"])) {

                    die("CAIO");

                    $order = Mage::getModel('sales/order')->loadByIncrementId($data["latest_order_id"]);
                    $order_store_id = $order->getStore()->getId();

                    if (in_array($order_store_id, $storeIds)) {
                        $data = array_merge(
                            $data, array(
                                "lang" => Mage::app()->getStore($order->getStoreId())->getName(),
                                "store_name" => $order->getStoreGroupName(),
                                "website_name" => Mage::app()->getStore($order->getStoreId())->getWebsite()->getName(),
                            )
                        );
                        $isFound = true;
                    }

                }

            }*/

            if (!$isFound) {
                return false;
            }

        }

        if ($customerAddressId) {

            /**
             * @var $address \Mage_Customer_Model_Address
             */

            $address = Mage::getModel('customer/address')->load(
                $customerAddressId
            );

            $data = array_merge(
                $data, array(
                    "billing_company" => $address->getData("company"),
                    "billing_address_1" => $address->getData('street'),
                    "billing_postcode" => $address->getData("postcode"),
                    "billing_city" => $address->getData("city"),
                    "billing_state" => $address->getData("region"),
                    "billing_country" => $address->getCountry(),
                    "billing_phone" => $address->getData('telephone'),
                    "billing_phone_2" => $address->getData("fax"),

                )
            );
        }

        if (!empty($stores)) {
            $data = array_merge($data, $stores);
        }

        return $data;

    }

    public
    function getCustomersData(
        $action = "no",
        $storeIds = array()
    )
    {
        $model = Mage::getModel("customer/customer");

        $customerCollection = $model->getCollection();
        $customersCollection = array();

        foreach ($customerCollection as $customerCollectionId) {

            if (is_object($customerCollectionId)) {
                $currentCustomerId = $customerCollectionId->getId();
            }

            if (!$currentCustomerId) {
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
     *
     * @return array
     */

    public
    function getSyncOrderData(
        $order
    )
    {

        $customerId = $order->getCustomerId();
        $model = Mage::getModel("customer/customer");

        /**
         * @var $customer \Mage_Customer_Model_Customer
         */

        $customer = $model->load($customerId);
        $gender_id = $customer->getAttribute('gender')->getSource()
            ->getOptionId($customer->getGender());

        $customerAddressId = $customer->getDefaultBilling();

        /**
         * @var $gender \Dueclic_Emailchef_Helper_Customer
         */

        $grand_total = $this->getTotalOrdered($customer->getId(), false);

        if (!$order->getCustomerIsGuest()) {

            $data = array(
                "customer_id" => $customer->getId(),
                "customer_type" => Mage::getModel('customer/group')->load(
                    $customer->getGroupId()
                )->getCustomerGroupCode(),
                "customer_created" => date("Y-m-d", $customer->getCreatedAtTimestamp()),
                "first_name" => $customer->getFirstname(),
                "last_name" => $customer->getLastname(),
                "user_email" => $customer->getEmail(),
                "source" => "eMailChef for Magento",
                "gender" => $this->getGenderStatus($gender_id),
                "birthday" => $this->getDateFromDateTime($customer->getDob()),
                "currency" => $order->getOrderCurrencyCode(),
            );

        } else {

            $data = array(
                "customer_id" => $order->getCustomerId(),
                "customer_type" => Mage::getModel('customer/group')->load(
                    $order->getCustomerGroupId()
                )->getCustomerGroupCode(),
                "customer_created" => date("Y-m-d", $customer->getCreatedAtTimestamp()),
                "first_name" => $order->getCustomerFirstname(),
                "last_name" => $order->getCustomerLastname(),
                "user_email" => $order->getCustomerEmail(),
                "source" => "eMailChef for Magento",
                "gender" => $this->getGenderStatus($order->getCustomerGender()),
                "birthday" => $this->getDateFromDateTime($order->getCustomerDob()),
                "currency" => $order->getOrderCurrencyCode(),
            );
        }

        $data = array_merge($data, $grand_total);

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
            if (!in_array($item->getProductId(), $all_items_order)) {
                $all_items_order[] = $item->getProductId();
            }
        }

        $latest_order["latest_order_product_ids"] = implode(",", $all_items_order);

        $data = array_merge($data, $latest_order);

        $address = $order->getBillingAddress();

        $order_address = array(
            "lang" => Mage::app()->getStore($order->getStoreId())->getName(),
            "store_name" => $order->getStoreGroupName(),
            "website_name" => Mage::app()->getStore($order->getStoreId())->getWebsite()->getName(),
            "billing_company" => $address->getData("company"),
            "billing_address_1" => $address->getData('street'),
            "billing_postcode" => $address->getData("postcode"),
            "billing_city" => $address->getData("city"),
            "billing_state" => $address->getData("region"),
            "billing_country" => $address->getCountry(),
            "billing_phone" => $address->getData('telephone'),
            "billing_phone_2" => $address->getData("fax"),
        );

        $data = array_merge($data, $order_address);

        $totals = array(
            "total_ordered_30d" => self::_formatPrice(
                $latest_order["latest_order_amount"]
            ),
            "total_ordered_12m" => self::_formatPrice(
                $latest_order["latest_order_amount"]
            ),
            "all_ordered_product_ids" => $latest_order["latest_order_product_ids"],
            "total_orders" => 1,
            "total_ordered" => self::_formatPrice(
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

    public
    function flushAbandonedCarts()
    {
        return array(
            'ab_cart_prod_name_pr_hr' => '',
            'ab_cart_prod_desc_pr_hr' => '',
            'ab_cart_prod_pr_pr_hr' => '',
            'ab_cart_date' => '',
            'ab_cart_prod_id_pr_hr' => '',
            'ab_cart_prod_url_pr_hr' => '',
            'ab_cart_prod_url_img_pr_hr' => '',
            'ab_cart_is_abandoned_cart' => false,
        );
    }

}