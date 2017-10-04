<?php

/**
 * Sample Widget Helper
 */
class Dueclic_Emailchef_Helper_Customfield extends Mage_Core_Helper_Abstract
{

    public function getStoreViews()
    {
        $storeviews = array();
        foreach (Mage::app()->getWebsites() as $website) {
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                foreach ($stores as $store) {
                    $storeviews[] = array(
                        "text" => $store->getName(),
                    );
                }
            }
        }

        return $storeviews;
    }

    public function getCurrencies()
    {
        $currencies = array();
        $codes      = Mage::app()->getStore()->getAvailableCurrencyCodes(true);
        foreach ($codes as $code) {
            $currencies[] = array(
                "text" => $code,
            );
        }

        return $currencies;
    }

    public function getOrderStatuses()
    {
        $statuses     = array();
        $order_status = Mage::getModel('sales/order_status')
            ->getResourceCollection()->getData();
        foreach ($order_status as $status) {
            $statuses[] = array(
                "text" => $status["label"],
            );
        }

        return $statuses;
    }

    public function getOrderStatusDelivered()
    {
        $statuses = array(
            array(
                "text" => "Complete",
            ),
            array(
                "text" => "Processing"
            )
        );

        return $statuses;
    }

    public function getCustomerGroups()
    {
        $groups_col = array();
        $groups     = Mage::getModel('customer/group')->getCollection();
        foreach ($groups as $group) {
            $groups_col[] = array(
                "text" => $group->getCustomerGroupCode(),
            );
        }

        return $groups_col;
    }

    public function getCustomFields()
    {
        return array(

            'first_name'                  => array(
                'name'      => $this->__('Name'),
                'data_type' => 'predefined',
                'ord'       => 0,
            ),
            'last_name'                   => array(
                'name'      => $this->__('Surname'),
                'data_type' => 'predefined',
                'ord'       => 1,
            ),
            'user_email'                  => array(
                'name'      => $this->__('Email address'),
                'data_type' => 'predefined',
                'ord'       => 2,
            ),
            'source'                      => array(
                'name'      => $this->__('Source'),
                'data_type' => 'text',
                'ord'       => 3,
            ),
            'gender'                      => array(
                'name'          => $this->__('Gender'),
                'data_type'     => 'select',
                'options'       => array(
                    array(
                        'text' => 'na',
                    ),
                    array(
                        'text' => 'm',
                    ),
                    array(
                        'text' => 'f',
                    ),
                ),
                'default_value' => 'na',
                'ord'           => 4,
            ),
            'lang'                        => array(
                'name'          => $this->__("Language"),
                'data_type'     => 'select',
                'options'       => $this->getStoreViews(),
                'default_value' => Mage::app()->getDefaultStoreView()->getName(
                ),
                'ord'           => 5,
            ),
            'birthday'                    => array(
                'name'      => $this->__("Date of birth"),
                'data_type' => 'date',
                'ord'       => 6,
            ),
            'billing_company'             => array(
                'name'      => $this->__("Company"),
                'data_type' => 'text',
                'ord'       => 7,
            ),
            'billing_address_1'           => array(
                'name'      => $this->__("Address"),
                'data_type' => 'text',
                'ord'       => 8,
            ),
            'billing_postcode'            => array(
                'name'      => $this->__("ZIP code"),
                'data_type' => 'text',
                'ord'       => 9,
            ),
            'billing_city'                => array(
                'name'      => $this->__("City"),
                'data_type' => 'text',
                'ord'       => 10,
            ),
            'billing_state'               => array(
                'name'      => $this->__("State"),
                'data_type' => 'text',
                'ord'       => 11,
            ),
            'billing_country'             => array(
                'name'      => $this->__("Country"),
                'data_type' => 'text',
                'ord'       => 12,
            ),
            'billing_phone'               => array(
                'name'      => $this->__("Phone"),
                'data_type' => 'text',
                'ord'       => 13,
            ),
            'billing_phone_2'             => array(
                'name'      => $this->__('Fax'),
                'data_type' => 'text',
                'ord'       => 14,
            ),
            'newsletter'                  => array(
                'name'          => $this->__('Agreed to newsletter'),
                'data_type'     => 'select',
                'options'       => array(
                    array(
                        'text' => 'yes',
                    ),
                    array(
                        'text' => 'no',
                    ),
                    array(
                        'text' => 'pending',
                    ),
                ),
                'default_value' => 'no',
                'ord'           => 15,
            ),
            'currency'                    => array(
                'name'          => $this->__('Currency'),
                'data_type'     => 'select',
                'options'       => $this->getCurrencies(),
                'default_value' => Mage::app()->getStore()
                    ->getCurrentCurrencyCode(),
                'ord'           => 16,
            ),
            'customer_id'                 => array(
                'name'      => $this->__("Customer ID"),
                'data_type' => 'number',
                'ord'       => 17,
            ),
            'customer_type'               => array(
                'name'      => $this->__("Customer type"),
                'data_type' => 'select',
                'options'   => $this->getCustomerGroups(),
                'ord'       => 18,
            ),
            'total_ordered'               => array(
                'name'      => $this->__("Subtotal"),
                'data_type' => 'number',
                'ord'       => 19,
            ),
            'total_ordered_30d'           => array(
                'name'      => $this->__("Total ordered in the last 30 days"),
                'data_type' => 'number',
                'ord'       => 20,
            ),
            'total_ordered_12m'           => array(
                'name'      => $this->__("Total ordered in the last 12 months"),
                'data_type' => 'number',
                'ord'       => 21,
            ),
            'total_orders'                => array(
                'name'      => $this->__("Orders"),
                'data_type' => 'number',
                'ord'       => 22,
            ),
            'all_ordered_product_ids'     => array(
                'name'      => $this->__("Ordered Product IDs"),
                'data_type' => 'text',
                'ord'       => 23,
            ),
            'latest_order_id'             => array(
                'name'      => $this->__("Last order - ID"),
                'data_type' => 'text',
                'ord'       => 24,
            ),
            'latest_order_date'           => array(
                'name'      => $this->__("Last order - Date"),
                'data_type' => 'date',
                'ord'       => 25,
            ),
            'latest_order_amount'         => array(
                'name'      => $this->__("Last order - Total"),
                'data_type' => 'number',
                'ord'       => 26,
            ),
            'latest_order_status'         => array(
                'name'      => $this->__("Last order - Status"),
                'data_type' => 'select',
                'options'   => $this->getOrderStatuses(),
                'ord'       => 27,
            ),
            'latest_order_product_ids'    => array(
                'name'      => $this->__("Last order - Product IDs"),
                'data_type' => 'text',
                'ord'       => 28,
            ),
            'latest_shipped_order_id'     => array(
                'name'      => $this->__("Last shipped order - IDs"),
                'data_type' => 'text',
                'ord'       => 29,
            ),
            'latest_shipped_order_date'   => array(
                'name'      => $this->__("Last shipped order - Data"),
                'data_type' => 'date',
                'ord'       => 30,
            ),
            'latest_shipped_order_status' => array(
                'name'      => $this->__("Last shipped order - Status"),
                'data_type' => 'select',
                'options'   => $this->getOrderStatusDelivered(),
                'ord'       => 31,
            ),
            'ab_cart_is_abandoned_cart'   => array(
                'name'          => $this->__("Abandoned cart - Yes/No"),
                'data_type'     => 'boolean',
                'ord'           => 32,
                'default_value' => 'no',
            ),
            'ab_cart_prod_name_pr_hr'     => array(
                'name'      => $this->__(
                    "Abandoned cart - Most expensive product name"
                ),
                'data_type' => 'text',
                'ord'       => 33,
            ),
            'ab_cart_prod_desc_pr_hr'     => array(
                'name'      => $this->__(
                    "Abandoned cart - Most expensive product description"
                ),
                'data_type' => 'text',
                'ord'       => 34,
            ),
            'ab_cart_prod_pr_pr_hr'       => array(
                'name'      => $this->__(
                    "Abandoned cart - Most expensive pricing product"
                ),
                'data_type' => 'number',
                'ord'       => 35,
            ),
            'ab_cart_prod_url_pr_hr'      => array(
                'name'      => $this->__(
                    'Abandoned cart - Most expensive product URL'
                ),
                'data_type' => 'text',
                'ord'       => 36,
            ),
            'ab_cart_prod_url_img_pr_hr'  => array(
                'name'      => $this->__(
                    'Abandoned cart - Most expensive product image URL'
                ),
                'data_type' => 'text',
                'ord'       => 37,
            ),
            'ab_cart_prod_id_pr_hr'       => array(
                'name'      => $this->__(
                    'Abandoned cart - Most expensive product ID'
                ),
                'data_type' => 'number',
                'ord'       => 38,
            ),
            'ab_cart_date'                => array(
                'name'      => $this->__('Abandoned cart - Date'),
                'data_type' => 'date',
                'ord'       => 39,
            ),

        );
    }

}