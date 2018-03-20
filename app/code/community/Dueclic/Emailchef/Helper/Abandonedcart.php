<?php

    /**
     * Sample Widget Helper
     */
class Dueclic_Emailchef_Helper_Abandonedcart extends Mage_Core_Helper_Abstract
{

    public function getDateFromDateTime($datetime)
    {
        if (empty($datetime)) {
            return '';
        }

        return date('Y-m-d', strtotime($datetime));
    }

    /**
     * @param $cart \Mage_Sales_Model_Quote
     * @return array
     */

    public function get($cart){

        $abandoned = array(
            "ab_cart_is_abandoned_cart" => true,
            "ab_cart_prod_name_pr_hr" => "",
            "ab_cart_prod_desc_pr_hr" => "",
            "ab_cart_prod_pr_pr_hr" => null,
            "ab_cart_prod_url_pr_hr" => "",
            "ab_cart_prod_url_img_pr_hr" => "",
            "ab_cart_prod_id_pr_hr" => "",
            "ab_cart_date" => ""
        );

        /**
         * @var $cart_item \Mage_Sales_Model_Quote_Item
         * @var $item \Mage_Catalog_Model_Product
         */

        foreach ($cart->getAllItems() as $cart_item){

            $item = Mage::getModel('catalog/product')->load($cart_item->getProductId());

            if ($item->getPrice() > $abandoned["ab_cart_prod_pr_pr_hr"] || $abandoned["ab_cart_prod_pr_pr_hr"] == null){
                $abandoned["ab_cart_prod_pr_pr_hr"] = number_format((float)$item->getPrice(), 2, '.', '');
                $abandoned["ab_cart_prod_name_pr_hr"] = $item->getName();
                $abandoned["ab_cart_prod_desc_pr_hr"] = $item->getShortDescription();
                $abandoned["ab_cart_prod_url_pr_hr"] = $item->getUrlInStore();
                $abandoned["ab_cart_prod_id_pr_hr"] = $item->getId();
                $abandoned["ab_cart_prod_url_img_pr_hr"] = $item->getImageUrl();
                $abandoned["ab_cart_date"] = $this->getDateFromDateTime($cart->getUpdatedAt());
                $abandoned["user_email"] = $cart->getCustomerEmail();
                $abandoned["firstname"] = $cart->getCustomerFirstname();
                $abandoned["lastname"] = $cart->getCustomerLastname();

            }
        }

        return $abandoned;
    }

}