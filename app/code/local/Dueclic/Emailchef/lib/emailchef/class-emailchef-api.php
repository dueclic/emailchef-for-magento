<?php

use \Httpful\Request as Request;
use \Httpful\Http as Http;
use \Httpful\Httpful as Httpful;
use \Httpful\Mime as Mime;
use \Httpful\Handlers\JsonHandler as JsonHandler;

class MG_Emailchef_Api {

	protected $api_url = "https://app.emailchef.com/api";
	public $lastError;
	private $isLogged = false;
	private $authkey = false;

	public function __construct( $username, $password ) {
		$this->process_login( $username, $password );
	}

	public function isLogged() {
		return $this->isLogged;
	}

	private function process_login( $username, $password ) {

		$response = $this->get( "/login", array(

			'username' => $username,
			'password' => $password

		), "POST", true );

		if ( ! isset( $response['authkey'] ) ) {
			$this->lastError = $response['message'];
		} else {
			$this->authkey  = $response['authkey'];
			$this->isLogged = true;
		}

	}

	private function getRequest( $url, $payload, $type, $action = "" ) {

		try {

			Httpful::register(
				Mime::JSON,
				new JsonHandler(
					array( 'decode_as_array' => true )
				)
			);

			$response = null;
			switch ( $type ) {
				case 'POST':
					$response = Request::post( $url )
					                   ->strictSSL( 1 )
					                   ->body( $payload, 'application/json' )
					                   ->send();
					break;
				case 'DELETE':
					$response = Request::init( Http::DELETE )
					                   ->strictSSL( 1 )
					                   ->uri( $url )
					                   ->body( $payload, 'application/json' )
					                   ->send();
					break;
				case 'PUT':
					$response = Request::put( $url )
					                   ->strictSSL( 1 )
					                   ->body( $payload, 'application/json' )
					                   ->send();
					break;
				case 'GET':
				default:
					$response = Request::get( $url )
					                   ->strictSSL( 1 )
					                   ->body( $payload, 'application/json' )
					                   ->send();
					break;
			}
		} catch ( \Exception $e ) {
			$response = array(
				'status' => 'error',
				'error'  => $e->getMessage()
			);
		}

		return $response;
	}

	protected function get( $route, $args = array(), $type = "POST", $encoded = false, $action = false ) {

		$url  = $this->api_url . $route;
		$auth = array();

		if ( $this->authkey !== false ) {
			$auth = array(
				'authkey' => $this->authkey
			);
		}

		$payload = array_merge( $auth, $args );

		if ( $encoded ) {
			$payload = json_encode( $payload );
		}

		return json_decode( $this->getRequest( $url, $payload, $type, $action ), true );
	}

}
