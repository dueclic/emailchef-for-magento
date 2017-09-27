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
            return "f";
        }

        return "na";
    }

    public function getStoreIdByCustomerCountryId($countryIdCustomer)
    {
        $countryIdReturn = null;
        $countryIdCustomer = trim((string)$countryIdCustomer);
        if (!strlen($countryIdCustomer)) {
            return false;
        }
        foreach (Mage::app()->getStores() as $store) {
            if (!$store->getIsActive()) {
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

    public function getTotalOrdered($customer_id)
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
        $lastOrderIds = array();

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

            if ($order->hasShipments() and ($order->getId()
                    > $lastShipmentOrderId)
            ) {
                $lastShipmentOrderId     = $order->getId();
                $lastShipmentOrderDate   = self::getDateFromDateTime(
                    $order->getCreatedAt()
                );
                $lastShipmentOrderStatus = $order->getStatus();
            }

            $items = $order->getAllItems();

            if ($lastOrderDate == null)
                $lastOrderDate = $order->getCreatedAt();
            else {
                if ($order->getCreatedAt() > $lastOrderDate )
                    $lastOrderDate = $order->getCreatedAt();
            }

            foreach ($items as $item) {
                if ( ! in_array($item->getProductId(), $allProductsIds)) {
                    $allProductsIds[] = $item->getProductId();
                }

                if ( strtotime($order->getCreatedAt()) == strtotime($lastOrderDate) ) {
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
        $latest_order_id     = end($allOrdersIds);
        $latest_order_status = end($allOrdersStatuses);

        $report = array(
            "total_ordered_30d"       => self::_formatPrice(
                $last30daysOrdersAmount
            ),
            "total_ordered_12m"       => self::_formatPrice(
                $last12monthsOrdersAmount
            ),
            "all_ordered_product_ids" => implode(", ", $allProductsIds),
            "total_orders"            => count($allOrdersIds),
            "total_ordered"           => self::_formatPrice(
                $allOrdersTotalAmount
            ),
            "latest_order_amount"     => $latest_order_amount,
            "latest_order_date"       => $latest_order_date,
            "latest_order_id"         => $latest_order_id,
            "latest_order_status"     => $latest_order_status,
            "latest_order_product_ids"    => implode(",", $lastOrderIds),
            "latest_shipped_order_id"     => $lastShipmentOrderId,
            "latest_shipped_order_date"   => $lastShipmentOrderDate,
            "latest_shipped_order_status" => $lastShipmentOrderStatus,
            "ab_cart_is_abandoned_cart"   => "no",
        );


        return $report;

    }

}