<?php

require_once(dirname(__DIR__) . "/vendor/autoload.php");
require_once(dirname(__FILE__) . '/class-emailchef-api.php');

class MG_Emailchef extends MG_Emailchef_Api
{

    public $lastError;
    public $lastResponse;
    private $new_custom_id;
    private $temp_custom_id = array();

    /**
     * @var $instance \MG_Emailchef
     */

    private static $instance = false;

    public function __construct($username, $password)
    {
        parent::__construct($username, $password);
        $this->api_url = "https://app.emailchef.com/apps/api/v1";
    }

    public static function getInstance($username, $password)
    {

        self::$instance = new self($username, $password);

        return self::$instance;

    }

    /**
     * Get policy of account
     *
     * @return string
     */

    public function get_policy()
    {
        $account = $this->get("/accounts/current", array(), "GET");
        return $account['mode'];
    }

    /**
     * Get integrations
     *
     * @return string
     */

    public function get_meta_integrations()
    {
        $integrations = $this->get("/meta/integrations", array(), "GET");
        return $integrations;
    }


    /**
     * Get integrations from eMailChef List
     *
     * @param $list_id
     *
     * @return mixed
     */

    public function get_integrations($list_id)
    {
        $route = sprintf("/lists/%d/integrations", $list_id);
        return $this->get($route, array(), "GET", false, "debug");
    }

    /**
     * Upsert integrations yo eMailChef List
     *
     * @param $list_id
     *
     * @return mixed
     */

    public function upsert_integration($list_id) {
        $integrations = $this->get_integrations($list_id);
        foreach ($integrations as $integration) {
            if ($integration["id"] == 1 && $integration["website"] == Mage::getBaseUrl( Mage_Core_Model_Store::URL_TYPE_WEB, true ))
                return $this->update_integration($integration["row_id"], $list_id);
        }
        return $this->create_integration($list_id);
    }

    /**
     * Upsert integrations yo eMailChef List
     *
     * @param $list_id
     * @param $integration_id
     *
     * @return mixed
     */

    public function update_integration($integration_id, $list_id) {

        $args = array(

            "instance_in" => array(
                "list_id" => $list_id,
                "integration_id" => 1,
                "website" => Mage::getBaseUrl( Mage_Core_Model_Store::URL_TYPE_WEB, true ),
            )

        );

        $response = $this->get("/integrations/".$integration_id, $args, "PUT");

        if ($response['status'] != "OK") {
            $this->lastError    = $response['message'];
            $this->lastResponse = $response;

            return false;
        }

        return $response['integration_id'];

    }

    /**
     * Get integrations from eMailChef List
     *
     * @param $list_id
     *
     * @return mixed
     */

    public function create_integration($list_id) {

        $args = array(

            "instance_in" => array(
                "list_id" => $list_id,
                "integration_id" => 1,
                "website" => Mage::getBaseUrl( Mage_Core_Model_Store::URL_TYPE_WEB, true ),
            )

        );

        $response = $this->get("/integrations", $args, "POST");

        if ($response['status'] != "OK") {
            $this->lastError    = $response['message'];
            $this->lastResponse = $response;

            return false;
        }

        return $response['id'];

    }

    /**
     * Get lists from eMailChef
     *
     * @param array $args
     * @param bool $asArray
     *
     * @return mixed
     */

    private function lists($args = array(), $asArray = true)
    {
        return $this->get("/lists", $args, "GET");
    }

    /**
     * Get lists in a format valid for PrestaShop
     *
     * @return array|bool
     */

    public function get_lists()
    {
        $args['offset']    = 0;
        $args['orderby']   = 'cd';
        $args['ordertype'] = 'd';

        if ( ! array_key_exists('limit', $args)) {
            $args['limit'] = 100;
        }

        $lists = $this->lists($args);

        if ( ! $lists) {
            return false;
        }

        $results = array();

        foreach ($lists as $list) {
            $results[] = array(
                'value' => $list['id'],
                'label' => $list['name'],
            );
        }

        return $results;
    }

    /**
     * Get collection of custom fields from eMailChef List
     *
     * @param $list_id
     *
     * @return mixed
     */

    public function get_collection($list_id)
    {
        $route = sprintf("/lists/%d/customfields", $list_id);

        return $this->get($route, array(), "GET", false, "debug");
    }

    /**
     * Get ID of custom field in eMailChef collection
     *
     * @param        $list_id
     * @param string $placeholder
     *
     * @return mixed
     */

    public function get_custom_field_id($list_id, $placeholder)
    {
        $collection = $this->get_collection($list_id);

        foreach ($collection as $custom_field) {

            if ($custom_field['place_holder'] == $placeholder) {
                return $custom_field['id'];
            }

        }

        return false;
    }

    /**
     * Get custom fields from Config
     *
     * @return mixed
     */

    protected function get_custom_fields()
    {

        /**
         * @var $helper \Dueclic_Emailchef_Helper_Customfield
         */

        $helper = Mage::helper("dueclic_emailchef/customfield");

        return $helper->getCustomFields();

    }

    /**
     * Initialize custom fields for eMailChef List ID
     *
     * @param $list_id
     *
     * @return bool
     */
    public function initialize_custom_fields($list_id)
    {

        $collection = $this->get_collection($list_id);

        $new_custom_fields = array();

        foreach ($this->get_custom_fields() as $place_holder => $custom_field) {

            $type          = $custom_field['data_type'];
            $name          = $custom_field['name'];
            $options       = (isset($custom_field['options'])
                ? $custom_field['options'] : array());
            $default_value = (isset($custom_field['default_value'])
                ? $custom_field['default_value'] : "");

            /**
             *
             * Check if is predefined
             * if it is continue
             *
             */

            if ($type == "predefined") {
                continue;
            }

            /**
             *
             * Check if a custom field exists by placeholder
             *
             */

            $cID = array_search(
                $place_holder, array_column($collection, "place_holder")
            );

            if ($cID !== false) {

                /**
                 *
                 * Check if the type of custom fields is valid
                 *
                 */

                $data_type = $collection[$cID]['data_type'];
                $data_id   = $collection[$cID]['id'];

                if ($type != $data_type) {
                    $this->delete_custom_field($data_id);
                } else {
                    $new_custom_fields[] = $data_id;
                    continue;
                }

            }

            $this->create_custom_field(
                $list_id, $type, $name, $place_holder, $options, $default_value
            );
            $new_custom_fields[] = $this->new_custom_id;

        }

        /**
         *
         * Check if there are fields in emailChef
         * not present in @private $custom_fields
         *
         * If fields are present delete
         *
         */

        //$ec_id_custom_fields = array_column( $collection, "id" );
        //$diff = array_diff($ec_id_custom_fields, $new_custom_fields);

        /*foreach ($diff as $custom_id) {
            $this->delete_custom_field($custom_id);
        }*/

        return true;

    }

    /**
     * Create eMailChef List
     *
     * @param $name
     * @param $description
     *
     * @return bool
     */

    public function create_list($name, $description)
    {

        $args = array(

            "instance_in" => array(
                "list_name" => $name,
                "integrations" => array(
                    array(
                        "integration_id" => 1,
                        "website" => Mage::getBaseUrl( Mage_Core_Model_Store::URL_TYPE_WEB, true )
                    )
                )
            ),

        );

        if ($description != "") {
            $args["instance_in"]["list_description"] = $description;
        }

        $response = $this->get("/lists", $args, "POST");

        if ($response['status'] != "OK") {
            $this->lastError    = $response['message'];
            $this->lastResponse = $response;

            return false;
        }

        return $response['list_id'];

    }

    public function create_segment($list_id, $logic, $name, $description, $conditions)
    {

        $args = array(
            "instance_in" => array(
                "list_id"          => $list_id,
                "logic"            => $logic,
                "condition_groups" => $conditions,
                "name"             => $name,
                "description"      => $description
            )
        );

        $response = $this->get("/segments", $args, "POST");

        if ($response['status'] != "OK") {
            $this->lastError    = $response['message'];
            $this->lastResponse = $response;

            return false;
        }

        return $response['id'];

    }

    /**
     * Delete Custom Field
     *
     * @param $field_id
     *
     * @return bool
     */

    public function delete_custom_field($field_id)
    {

        $route = sprintf("/customfields/%d", $field_id);

        $status = $this->get($route, array(), "DELETE", true);

        if ($status !== "OK") {
            $this->lastError = $status['message'];
        }

        return ($status == "OK");

    }

    /**
     * Create a Custom Field in List ID
     *
     * @param        $list_id
     * @param        $type
     * @param string $name
     * @param        $placeholder
     * @param array $options
     * @param string $default_value
     *
     * @return bool
     */
    public function create_custom_field(
        $list_id,
        $type,
        $name = "",
        $placeholder,
        $options = array(),
        $default_value = ""
    ) {

        $route = sprintf("/lists/%d/customfields", $list_id);

        $args = array(

            "instance_in" => array(
                "data_type"     => $type,
                "name"          => ($name == "" ? $placeholder : $name),
                "place_holder"  => $placeholder,
                "default_value" => $default_value,
            ),

        );

        if ($type == "select") {
            $args["instance_in"]["options"] = $options;
        }

        $response = $this->get($route, $args, "POST", true);

        if (isset($response['status']) && $response['status'] == "OK") {

            $this->new_custom_id                = $response['custom_field_id'];
            $this->temp_custom_id[$placeholder] = $this->new_custom_id;

            return true;
        }

        $this->lastError = $response['message'];

        return false;


    }

    /**
     * Get temporary custom field by placeholder
     *
     * @param $placeholder
     *
     * @return int
     */

    public function get_temp_custom_id($placeholder)
    {
        return $this->temp_custom_id[$placeholder];
    }

    /**
     * Get temporary custom fields
     *
     * @return array
     */

    public function get_temp_custom_fields()
    {
        return $this->temp_custom_id;
    }

    /**
     * Update a Custom Field in List ID
     *
     * @param        $list_id
     * @param        $type
     * @param string $name
     * @param        $placeholder
     * @param array $options
     * @param string $default_value
     *
     * @return bool
     */
    public function update_custom_field(
        $list_id,
        $type,
        $name = "",
        $placeholder,
        $options = array(),
        $default_value = ""
    ) {

        $collection = $this->get_collection($list_id);

        $cID = array_search(
            $placeholder, array_column($collection, "place_holder")
        );

        if ($cID === false) {
            $this->lastError = "Placeholder non valido.";

            return false;
        }

        $route = sprintf("/customfields/%d", $collection[$cID]['id']);

        $args = array(

            "instance_in" => array(
                "data_type"     => $type,
                "name"          => ($name == "" ? $placeholder : $name),
                "place_holder"  => $placeholder,
                "default_value" => $default_value,
            ),

        );

        $args["instance_in"]["data_type"]     = $type;
        $args["instance_in"]["name"]          = $name;
        $args["instance_in"]["place_holder"]  = $placeholder;
        $args["instance_in"]["default_value"] = $default_value;

        if ($type == "select") {
            $args["instance_in"]["options"] = $options;
        }

        $response = $this->get($route, $args, "PUT", true);

        if (isset($response['status']) && $response['status'] == "OK") {

            $this->new_custom_id = $response['custom_field_id'];

            return true;
        }

        $this->lastError = $response['message'];

        return false;

    }

	public function import( $list_id, $customers ) {

		$args = array(

			"instance_in" => array(
				"contacts"          => $customers,
				"notification_link" => ""
			)

		);

		$update = $this->get( "/lists/" . $list_id . "/import", $args, "POST" );

		if ( isset( $update['status'] ) && $update['status'] == "OK" ) {
			return true;
		}

		$this->lastError = $update['message'];

		return false;

	}

    /**
     *
     * Insert customer
     *
     * @param $list_id
     * @param $customer
     *
     * @return bool
     */

    private function insert_customer($list_id, $customer)
    {

        $collection = $this->get_collection((int)$list_id);

	    $custom_fields = array();

        foreach ($collection as $custom) {

            $my_custom = $custom;

            if ( ! isset($customer[$my_custom['place_holder']])) {
                continue;
            }

            $my_custom['value'] = $customer[$my_custom['place_holder']];

            $custom_fields[] = $my_custom;

        }

        $args = array(

            "instance_in" => array(
                "list_id"       => $list_id,
                "status"        => "ACTIVE",
                "email"         => $customer['user_email'],
                "firstname"     => $customer['first_name'],
                "lastname"      => $customer['last_name'],
                "custom_fields" => $custom_fields,
                "mode"          => "ADMIN",
            ),

        );

        if ( ! isset($customer["first_name"])) {
            unset($args["instance_in"]["firstname"]);
        }

        if ( ! isset($customer["last_name"])) {
            unset($args["instance_in"]["lastname"]);
        }

        $response = $this->get("/contacts", $args, "POST", false, "debug");

	    if ( isset( $response['contact_added_to_list'] ) && $response['contact_added_to_list'] ) {
		    return true;
	    }

	    if ( isset( $response['contact_added_to_list'] ) && !$response['contact_added_to_list'] && $response["updated"] ) {
		    return true;
	    }

        $this->lastError = $response['message'];

        return false;

    }

    /**
     *
     * Update customer
     *
     * @param $list_id
     * @param $customer
     * @param $ec_id
    '	 *
     *
     * @return bool
     */

    private function update_customer($list_id, $customer, $ec_id)
    {

        $path  = "/contacts";
        $route = sprintf("%s/%d", $path, $ec_id);

        $custom_fields = array();
        $collection    = $this->get_collection($list_id);

        foreach ($collection as $custom) {

            $my_custom = $custom;

            if ( ! isset($customer[$my_custom['place_holder']])) {
                continue;
            }

            $my_custom['value'] = $customer[$my_custom['place_holder']];

            $custom_fields[] = $my_custom;

        }

        $args = array(

            "instance_in" => array(
                "list_id"       => $list_id,
                "status"        => "ACTIVE",
                "email"         => $customer['user_email'],
                "firstname"     => $customer['first_name'],
                "lastname"      => $customer['last_name'],
                "custom_fields" => $custom_fields,
                "mode"          => "ADMIN",
            ),

        );

        if ( ! isset($customer["first_name"])) {
            unset($args["instance_in"]["firstname"]);
        }

        if ( ! isset($customer["last_name"])) {
            unset($args["instance_in"]["lastname"]);
        }

        $update = $this->get($route, $args, "PUT");

        if (isset($update['status']) && $update['status'] == "OK") {
            return true;
        }

        $this->lastError = $update['message'];

        return false;

    }

    /**
     *
     * Get list status
     *
     * @param $list_id
     *
     * @return bool
     */

    public function get_list_status($list_id)
    {

        $list_endpoint = sprintf("/lists/%d", (int)$list_id);
        $list          = $this->get($list_endpoint, array(), "GET");

        return $list;

    }

    /**
     *
     * Upsert customer
     *
     * @param $list_id
     * @param $customer
     *
     * @return bool
     */

    public function upsert_customer($list_id, $customer)
    {
	    return $this->insert_customer( $list_id, $customer );
    }

}