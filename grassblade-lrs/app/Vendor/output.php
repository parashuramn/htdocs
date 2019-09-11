<?php
class Output {
	public $messages = array(
		"INVALID_REQUEST_METHOD" => array("code" => 400, "message" => "Invalid Request Method"),
		"STATEMENT_POST_SUCCESS" => array("code" => 204, "message" => ""),
		);
	public function __construct() {
	}
	public function end($message_code, $append = "") {
		$code = $this->messages[$message_code]["code"];
		$message = $this->messages[$message_code]["message"].$append;
		$this->send($code, $message);
	}
	public function send($code, $message, $json = "") {
		//setting the headers to enable Cross Origin Requests
		if(!defined("DISABLE_SENDING_HEADERS")) {
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Max-Age: 3600");
		header("Access-Control-Allow-Methods: HEAD,GET,POST,PUT,DELETE");
		header("Access-Control-Allow-Headers: Content-Type,Content-Length,Authorization,If-Match,If-None-Match,X-Experience-API-Version,X-Experience-API-Consistent-Through");
		header("Access-Control-Expose-Headers: ETag,Last-Modified,Cache-Control,Content-Type,Content-Length,WWW-Authenticate,X-Experience-API-Version,X-Experience-API-Consistent-Through");
		header("Cache-Control: no-cache");
		}
		//header("X-Experience-API-Version: ".API_VERSION);
		header("X-Experience-API-Consistent-Through:".generate_time());
		//header("X-Experience-API-Execution-Time:".((microtime(true) - $_SERVER['REQUEST_TIME'])));
		if($json == "json") {
			header('Content-type: application/json; charset=UTF-8');
		}
		header(' ', true, $code);
		echo $message;

		if(!in_array($code, array(200, 204))) {
			add_error_log($code, $message);
		}
		die();
	}
}
