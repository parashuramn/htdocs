<?php
define("API_VERSION", "1.0.0");
		
class Input {
	public $version = null;
	public $method = null;
	public $headers = null;
	public $IE_MODE_DATA = null;
	public $content = null;
	public $params = null;
	
	public function __construct() {
		$this->CheckMethodAndFetchData();
	//	CakeLog::debug(print_r($this, true));
	}
	function IE_MODE_DATA() {
		$data = array();
		$content_vars = file_get_contents("php://input");
		if(!empty($content_vars) && strpos($content_vars, "&")) {
			$content_vars = explode("&", $content_vars);
			foreach($content_vars as $row){
				$row = explode("=", $row);
				if(isset($row[0]) && isset($row[1]))
				$data[urldecode($row[0])] = gb_urldecode($row[1]);
			}
		}
		$this->IE_MODE_DATA = $data;
		return $data;
	}
	function CheckMethodAndFetchData() {
		if(!empty($_GET["method"])) { // IE MODE
			$this->method = $_GET["method"];
			$this->headers = $this->params = $this->IE_MODE_DATA();
			$this->content = @$this->headers["content"];
			unset($this->headers["content"]);
		}
		else {
			$this->method = $_SERVER["REQUEST_METHOD"];
			$this->headers = getallheaders();
			$this->content = file_get_contents("php://input");
			$this->params = $_REQUEST;
		}
		if(empty($this->headers["Authorization"])) {
			if(!empty($_SERVER["REMOTE_USER"]))
				$this->headers["Authorization"] = $_SERVER["REMOTE_USER"];
			else if(!empty($_SERVER["REDIRECT_REMOTE_USER"]))
				$this->headers["Authorization"] = $_SERVER["REDIRECT_REMOTE_USER"];
		}
		if(!empty($this->headers) && is_array($this->headers))
		foreach ($this->headers as $key => $value) {
			$this->headers[trim($key)] = $value;
		}
		foreach($this->params as $key => $value) {
			if(is_string($value))
			$this->params[$key] = stripslashes($value);
		}		
		//Allow fetching statements using POST method
		if($this->method == "POST" && (!empty($this->params["limit"]) || !empty($this->params["sparse"]) || !empty($this->params["context"])))
			$this->method = "GET";
			
		if(!in_array($this->method, array("GET", "POST", "PUT", "DELETE"))) {
			$output = new Output();
			$output->end("INVALID_REQUEST_METHOD", " : ".$this->method);
		}
		
        if(!empty($this->params["agent"]))
                $this->params["agent"] = clean_agent($this->params["agent"]);
        else if(!empty($this->params["actor"]))
                $this->params["agent"] = clean_agent($this->params["actor"]);

		$this->CheckVersion();
		
		if(!defined("DISABLE_SENDING_HEADERS"))
		$this->AddHeaders();
        $this->headers = gb_urldecode($this->headers);
        $this->params = gb_urldecode($this->params);
		$this->content = str_replace(array("%0A", "%09", "%08", "%0D"), array("\\n", "\\t", "", "\\r"), $this->content); //Fix: New line and other encoded character causing issue with json decode
		$this->content = gb_urldecode($this->content);
		return $this->method;
	}
	function CheckVersion() {
		if(!empty($this->headers["X-Experience-Api-Version"]))
			$this->version = $this->headers["X-Experience-Api-Version"];

		if(!empty($this->headers["X-Experience-API-Version"]))
			$this->version = $this->headers["X-Experience-API-Version"];
		
		if(!empty($this->version))
		header("X-Experience-API-Version: ".$this->version);
		else
		header("X-Experience-API-Version: ".API_VERSION);
	
		return $this->version;
	}
	
	function AddHeaders() {
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Max-Age: 3600");
		header("Access-Control-Allow-Methods: HEAD,GET,POST,PUT,DELETE");
		header("Access-Control-Allow-Headers: Content-Type,Content-Length,Authorization,If-Match,If-None-Match,X-Experience-API-Version,X-Experience-API-Consistent-Through");
		header("Access-Control-Expose-Headers: ETag,Last-Modified,Cache-Control,Content-Type,Content-Length,WWW-Authenticate,X-Experience-API-Version,X-Experience-API-Consistent-Through");
	}	
	
	public function Content() {
		return $this->content;
	}
	public function Version() {
		return $this->version;
	}
	public function Method() {
		return $this->method;
	}
	public function Params() {
		return $this->params;
	}
}
