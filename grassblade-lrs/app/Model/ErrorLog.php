<?php
App::uses('AppModel', 'Model');
/**
 * ErrorLog Model
 *
 * @property Statement $Statement
 */
class ErrorLog extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = 'gb_error_log';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
	);

	// The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Statement' => array(
			'className' => 'Statement',
			'foreignKey' => 'statement_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	public function add($type, $data = array(), $status = 0) {
		/*
		$data =
			user
			statement_id
			url
			request_method
			data
			error_msg
			error_code
			response
		*/
		/*
		Other fields
			type
			IP
			created
			modified
			status
		*/
		
		$data["type"] = $type;
		$data["created"] = date("Y-m-d H:i:s");
		$data["modified"] = date("Y-m-d H:i:s");
		$data["status"] = $status;

		switch ($type) {
			case 'trigger':
				/* Important Parameters: 
					url
					request_method
					statement_id
					data = trigger type


					//On Error:
					error_msg
					error_code
					response

				*/


				$url = $data['url'];
				$parsedUrl = parse_url($url);
				$domain = $parsedUrl["host"];
				$IP = gethostbyname($domain);
				$data["IP"] = $IP;

				
				break;
			
			default:
				$data["IP"] = @$_SERVER["REMOTE_ADDR"];

				break;
		}
		if(is_array($data['data']))
			$data['data'] = json_encode($data['data']);
		$data = $this->save($data);

		return @$data["ErrorLog"]["id"];
	}
	public function update($id, $data = array(), $status = 0) {
		if(empty($id))
			return;
		$error_log = $this->find("first", array('conditions' => array('id' => $id)));

		if(empty($error_log))
			return;

		$error_log = $error_log['ErrorLog'];

		foreach ($data as $key => $value) {
			if(is_array($value))
				$value = json_encode($value);
			$error_log[$key] = $value;
		}
		$error_log["status"] = $status;
		$error_log["modified"] = date("Y-m-d H:i:s");

		$error_log = $this->save($error_log);
		return $error_log['ErrorLog'];

	}

}
