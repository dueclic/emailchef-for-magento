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
        $statuses   = array();
        $statuses[] = array(
            "text" => "Complete",
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
                'name'      => 'Nome',
                'data_type' => 'predefined',
                'ord'       => 0,
            ),
            'last_name'                   => array(
                'name'      => 'Cognome',
                'data_type' => 'predefined',
                'ord'       => 1,
            ),
            'user_email'                  => array(
                'name'      => 'Email',
                'data_type' => 'predefined',
                'ord'       => 2,
            ),
            'source'                      => array(
                'name'      => 'Sorgente',
                'data_type' => 'text',
                'ord'       => 3,
            ),
            'gender'                      => array(
                'name'          => 'Sesso',
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
                'name'          => 'Lingua',
                'data_type'     => 'select',
                'options'       => $this->getStoreViews(),
                'default_value' => Mage::app()->getDefaultStoreView()->getName(
                ),
                'ord'           => 5,
            ),
            'birthday'                    => array(
                'name'      => 'Data di nascita',
                'data_type' => 'date',
                'ord'       => 6,
            ),
            'billing_company'             => array(
                'name'      => 'Società',
                'data_type' => 'text',
                'ord'       => 7,
            ),
            'billing_address_1'           => array(
                'name'      => 'Indirizzo',
                'data_type' => 'text',
                'ord'       => 8,
            ),
            'billing_postcode'            => array(
                'name'      => 'CAP',
                'data_type' => 'text',
                'ord'       => 9,
            ),
            'billing_city'                => array(
                'name'      => 'Città',
                'data_type' => 'text',
                'ord'       => 10,
            ),
            'billing_state'               => array(
                'name'      => 'Provincia',
                'data_type' => 'text',
                'ord'       => 11,
            ),
            'billing_country'             => array(
                'name'      => 'Paese',
                'data_type' => 'text',
                'ord'       => 12,
            ),
            'billing_phone'               => array(
                'name'      => 'Telefono fisso',
                'data_type' => 'text',
                'ord'       => 13,
            ),
            'billing_phone_2'             => array(
                'name'      => 'Fax',
                'data_type' => 'text',
                'ord'       => 14,
            ),
            'newsletter'                  => array(
                'name'          => 'Consenso newsletter',
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
                'name'          => 'Valuta',
                'data_type'     => 'select',
                'options'       => $this->getCurrencies(),
                'default_value' => Mage::app()->getStore()
                    ->getCurrentCurrencyCode(),
                'ord'           => 16,
            ),
            'customer_id'                 => array(
                'name'      => 'ID Cliente',
                'data_type' => 'number',
                'ord'       => 17,
            ),
            'customer_type'               => array(
                'name'      => 'Tipo cliente',
                'data_type' => 'select',
                'options'   => $this->getCustomerGroups(),
                'ord'       => 18,
            ),
            'total_ordered'               => array(
                'name'      => 'Totale ordinato',
                'data_type' => 'number',
                'ord'       => 19,
            ),
            'total_ordered_30d'           => array(
                'name'      => 'Totale ordinato negli ultimi 30 giorni',
                'data_type' => 'number',
                'ord'       => 20,
            ),
            'total_ordered_12m'           => array(
                'name'      => 'Totale ordinato negli ultimi 12 mesi',
                'data_type' => 'number',
                'ord'       => 21,
            ),
            'total_orders'                => array(
                'name'      => 'Ordini totali',
                'data_type' => 'number',
                'ord'       => 22,
            ),
            'all_ordered_product_ids'     => array(
                'name'      => 'ID prodotti ordinati',
                'data_type' => 'text',
                'ord'       => 23,
            ),
            'latest_order_id'             => array(
                'name'      => 'Ultimo ordine - ID',
                'data_type' => 'text',
                'ord'       => 24,
            ),
            'latest_order_date'           => array(
                'name'      => 'Ultimo ordine - Data',
                'data_type' => 'date',
                'ord'       => 25,
            ),
            'latest_order_amount'         => array(
                'name'      => 'Ultimo ordine - Totale',
                'data_type' => 'number',
                'ord'       => 26,
            ),
            'latest_order_status'         => array(
                'name'      => 'Ultimo ordine - Stato lavorazione',
                'data_type' => 'select',
                'options'   => $this->getOrderStatuses(),
                'ord'       => 27,
            ),
            'latest_order_product_ids'    => array(
                'name'      => 'Ultimo ordine - ID prodotti',
                'data_type' => 'text',
                'ord'       => 28,
            ),
            'latest_shipped_order_id'     => array(
                'name'      => 'Ultimo ordine inviato - ID',
                'data_type' => 'text',
                'ord'       => 29,
            ),
            'latest_shipped_order_date'   => array(
                'name'      => 'Ultimo ordine inviato - Data',
                'data_type' => 'date',
                'ord'       => 30,
            ),
            'latest_shipped_order_status' => array(
                'name'      => 'Ultimo ordine inviato - Stato lavorazione',
                'data_type' => 'select',
                'options'   => $this->getOrderStatusDelivered(),
                'ord'       => 31,
            ),
            'ab_cart_is_abandoned_cart'   => array(
                'name'      => 'Carrello abbandonato - Sì/No',
                'data_type' => 'boolean',
                'ord'       => 32,
            ),
            'ab_cart_prod_name_pr_hr'     => array(
                'name'      => 'Carrello abbandonato - Nome prodotto più caro',
                'data_type' => 'text',
                'ord'       => 33,
            ),
            'ab_cart_prod_desc_pr_hr'     => array(
                'name'      => 'Carrello abbandonato - Desc. prodotto più caro',
                'data_type' => 'text',
                'ord'       => 34,
            ),
            'ab_cart_prod_pr_pr_hr'       => array(
                'name'      => 'Carrello abbandonato - Prezzo prodotto più caro',
                'data_type' => 'number',
                'ord'       => 35,
            ),
            'ab_cart_prod_url_pr_hr'      => array(
                'name'      => 'Carrello abbandonato - URL prodotto più caro',
                'data_type' => 'text',
                'ord'       => 36,
            ),
            'ab_cart_prod_url_img_pr_hr'  => array(
                'name'      => 'Carrello abbandonato - URL immagine prodotto più caro',
                'data_type' => 'text',
                'ord'       => 37,
            ),
            'ab_cart_prod_id_pr_hr'       => array(
                'name'      => 'Carrello abbandonato - ID prodotto più caro',
                'data_type' => 'number',
                'ord'       => 38,
            ),
            'ab_cart_date'                => array(
                'name'      => 'Carrello abbandonato - Data',
                'data_type' => 'date',
                'ord'       => 39,
            ),

        );
    }

}