<?php
class GBDB {
	public $db;
	public $config;
	function __construct() {
		global $db;
		if(empty($db))
			return false;
		$this->db = $db;
		$this->config = $this->db->config;
		if(isset($_REQUEST["upgradeTables"])) {
			$this->upgradeTables($_REQUEST["upgradeTables"]);
		}
	}
	function execute($sql) {
		return $this->db->rawQuery($sql);
	}
	function value($data) {
		return $this->db->value($data);
	}
	function name($data) {
		return $this->db->name($data);
	}
	function upgradeTables($version) {
		//$version = $this->db->getVersion(); //MySQL: 5.6.38  MariaDB: 5.5.5-10.2.12-MariaDB
		$DB_VERSION = grassblade_config_get('DB_VERSION', 0);

		$tables = $this->db->listSources();
		App::import('Model', 'Statement');
		$statementModel = new Statement();
		//App::import('Model', 'Config');
		//$ConfigModel = new Config();

		//echo '<pre>';print_r($ConfigModel->schema('value'));exit;

		$prefix = $this->db->config["prefix"];
		echo "<div class='upgradeTables ".$version." ".$DB_VERSION." ".LRS_VERSION."'><pre>";
		if($version == "1.0.1") {
			$return = $this->execute("ALTER TABLE  `".$prefix."gb_all_statements` ADD  `agent_id` VARCHAR( 1024 ) NULL AFTER  `agent_name` ;");
			print_r($return);
			$return = $this->execute("ALTER TABLE `".$prefix."gb_activities_state`  ADD `agent_id` VARCHAR(1024) NULL AFTER `agent_name`;");
			print_r($return);
			$return = $this->execute("ALTER TABLE `".$prefix."gb_agents_profile`  ADD `agent_id` VARCHAR(1024) NULL AFTER `agent_name`;");
			print_r($return);
			$return = $this->execute("UPDATE `".$prefix."gb_all_statements` SET `agent_id` = `agent_mbox` WHERE agent_id IS NULL;");
			print_r($return);
			$return = $this->execute("UPDATE `".$prefix."gb_activities_state` SET `agent_id` = `agent_mbox` WHERE agent_id IS NULL;;");
			print_r($return);
			$return = $this->execute("UPDATE `".$prefix."gb_agents_profile` SET `agent_id` = `agent_mbox` WHERE agent_id IS NULL;;");
			print_r($return);
		}
		if($version == "1.0.2") {
			$return = $this->execute("INSERT INTO `".$prefix."gb_triggers` (`name`, `type`, `criterion`, `target`, `status`, `modified`, `created`) VALUES
								( 'All Passed', 'completion', 'a:3:{s:7:\"verb_id\";s:37:\"http://adlnet.gov/expapi/verbs/passed\";s:14:\"result_success\";s:1:\"0\";s:17:\"result_completion\";s:1:\"0\";}', 'a:1:{s:3:\"All\";s:3:\"All\";}', 1, now(), now()),
								( 'All Completed', 'completion', 'a:3:{s:7:\"verb_id\";s:40:\"http://adlnet.gov/expapi/verbs/completed\";s:14:\"result_success\";s:1:\"0\";s:17:\"result_completion\";s:1:\"0\";}', 'a:1:{s:3:\"All\";s:3:\"All\";}', 1,now(), now());
								");
			print_r($return);
		}
		if(empty($statementModel->schema('authority_user_id'))) { //Version 2.0
			$return = $this->execute("ALTER TABLE  `".$prefix."gb_all_statements` ADD  `authority_user_id` INT NULL AFTER  `voided` ,
			ADD  `authority` VARCHAR( 256 ) NULL AFTER  `authority_user_id` ,
			ADD  `IP` VARCHAR( 64 ) NULL AFTER  `authority`");
			print_r($return);
		}
		if(empty( $statementModel->schema('agent_mbox')["null"])) { // 2.0 to 2.1.0
			$return = $this->execute("ALTER TABLE  `".$prefix."gb_all_statements` CHANGE  `agent_mbox`  `agent_mbox` VARCHAR( 1024 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL ;");
			print_r($return);
		}
		if( empty( $DB_VERSION ) || $DB_VERSION < "2.1.0" && $version >= "2.1.0" )    //$version >= "2.1.0" && $version <= "2.2.0") 
		{
			$return = $this->execute("ALTER TABLE `".$prefix."gb_config` CHANGE `value` `value` LONGTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
			print_r($return);
			$this->change_dursations_to_sec();
		}
		clear_cache();
		if($version >= LRS_VERSION)
		grassblade_config_set('DB_VERSION', LRS_VERSION);
		echo "</pre></div>";
	}
	function change_dursations_to_sec() {
		App::import('Model', 'Statement');
		App::import('Model', 'User');
		$statementModel = new Statement();
		set_time_limit(0);
        $limit = 2000;
        $count = 10000000;

		$statementModel->cacheQueries = false;
        $statementModel->recursive = 0;
        
        for ($offset = 0; $offset <= $count; $offset += $limit) {
        	//echo "<br><br>Offset : ".$offset. " limit: ".$limit." count: ".$count;
	 		$statements = $statementModel->find("all", array("fields" => array("id", "result_duration"), "conditions" => array("result_duration LIKE 'PT%'"), "limit" => $limit, "offset" => $offset, "maxLimit" => $limit, "nochange" => true ));
           
	 		foreach ($statements as $key => $statement) {
	 			//echo "<BR>".$statement["Statement"]["id"].":".$statement["Statement"]["result_duration"].":";
	 			$seconds = to_seconds($statement["Statement"]["result_duration"]);
	 			if($seconds != $statement["Statement"]["result_duration"]) {
	 			$statement["Statement"]["result_duration"] = to_seconds($statement["Statement"]["result_duration"]);
		 			$statementModel->save($statement);
		 		//	echo $statement["Statement"]["result_duration"];
	 			}
	 		}

            $result_count = count($statements); 
            if ($result_count < $limit) {
            	return;
            }
        }
	}
	function createTables() {
		$tables = $this->db->listSources();
		$prefix = $this->db->config["prefix"];
		$gb_tables = array(
				"gb_activities_profile",
				"gb_activities_state",
				"gb_agents_profile",
				"gb_all_statements",
				"gb_statements_continue",
				"gb_users",
				"gb_users_auth",
				"gb_id_translations",
				"gb_triggers",
				"gb_config",
				"gb_groups",
				"gb_group_agents",
				"gb_contents",
				"gb_content_types",
				"gb_error_log"
			);
		$gb_tables_sql = array(
			"gb_activities_profile" => "CREATE TABLE IF NOT EXISTS `".$prefix."gb_activities_profile` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `profileId` varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
			  `activityId` varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
			  `content_type` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
			  `content` longtext COLLATE utf8_unicode_ci NOT NULL,
			  `created` timestamp NULL DEFAULT NULL,
			  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`)
			)   DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;",

			"gb_activities_state" => "CREATE TABLE IF NOT EXISTS `".$prefix."gb_activities_state` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `stateId` varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
			  `agent` text COLLATE utf8_unicode_ci NOT NULL,
			  `registration` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `activityId` varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
			  `content_type` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
			  `content` longtext COLLATE utf8_unicode_ci NOT NULL,
			  `agent_name` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `agent_id` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `agent_mbox` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `agent_mbox_sha1sum` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `agent_openid` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `agent_account_homePage` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `agent_account_name` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `created` timestamp NULL DEFAULT NULL,
			  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`)
			)   DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;",

			"gb_agents_profile" => "CREATE TABLE IF NOT EXISTS `".$prefix."gb_agents_profile` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `profileId` varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
			  `agent` text COLLATE utf8_unicode_ci NOT NULL,
			  `content_type` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
			  `content` longtext COLLATE utf8_unicode_ci NOT NULL,
			  `agent_name` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `agent_id` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `agent_mbox` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `agent_mbox_sha1sum` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `agent_openid` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `agent_account_homePage` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `agent_account_name` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `created` timestamp NULL DEFAULT NULL,
			  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`)
			)   DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;",

			"gb_all_statements" => "CREATE TABLE IF NOT EXISTS `".$prefix."gb_all_statements` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `statement_id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
			  `registration` varchar(36) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `agent_name` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
			  `agent_id` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `agent_mbox` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `agent_mbox_sha1sum` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `agent_openid` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `agent_account_homePage` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `agent_account_name` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `user_id` int(11) NOT NULL DEFAULT '0',
			  `version` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
			  `verb_id` varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
			  `verb` varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
			  `objectid` varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
			  `object_objectType` varchar(512) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Activity',
			  `object_definition_type` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
			  `object_definition_name` varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
			  `object_definition_description` varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
			  `stored` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
			  `timestamp` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
			  `result_score_raw` double DEFAULT NULL,
			  `result_score_scaled` double DEFAULT NULL,
			  `result_score_min` double DEFAULT NULL,
			  `result_score_max` double DEFAULT NULL,
			  `result_completion` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
			  `result_success` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
			  `result_duration` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
			  `parent_ids` text COLLATE utf8_unicode_ci NOT NULL,
			  `grouping_ids` text COLLATE utf8_unicode_ci NOT NULL,
			  `statement` text COLLATE utf8_unicode_ci NOT NULL,
			  `headers` text COLLATE utf8_unicode_ci,
			  `voided` tinyint(1) DEFAULT NULL,
			  `authority_user_id` INT NULL,
			  `authority` VARCHAR( 256 ) NULL,
			  `IP` VARCHAR( 64 ) NULL,
			  `created` timestamp NULL DEFAULT NULL,
			  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `statement_id` (`statement_id`)
			)   DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;",
		
			"gb_statements_continue" => "CREATE TABLE IF NOT EXISTS `".$prefix."gb_statements_continue` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `continueToken` varchar(36) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `options` text COLLATE utf8_unicode_ci NOT NULL,
			  `created` timestamp NULL DEFAULT NULL,
			  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `continueToken` (`continueToken`)
			)   DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;",

			"gb_users" => "CREATE TABLE IF NOT EXISTS `".$prefix."gb_users` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT ' ',
			  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
			  `email` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
			  `role` varchar(16) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
			  `permissions` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
			  `created` timestamp NULL DEFAULT NULL,
			  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`)
			)   DEFAULT CHARSET=utf8 ;
			INSERT INTO `".$prefix."gb_users` (`id`, `name`, `password`, `email`, `role`, `permissions`, `created`, `modified`) VALUES
			(13, ' Admin', '21232f297a57a5a743894a0e4a801fc3', 'admin@nextsoftwaresolutions.com', 'admin', NULL, now(), now());
			",

			"gb_users_auth" => "CREATE TABLE IF NOT EXISTS `".$prefix."gb_users_auth` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `user_id` int(11) NOT NULL,
			  `api_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
			  `api_pass` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
			  `auth` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
			  `type` varchar(5) NOT NULL DEFAULT 'basic',
			  `permissions` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
			  `created` timestamp NULL DEFAULT NULL,
			  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`)
			)  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			",
			"gb_id_translations" => "CREATE TABLE IF NOT EXISTS `".$prefix."gb_id_translations` (
			  `pk` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `id` varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
			  `name` varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
			  `description` varchar(2048) COLLATE utf8_unicode_ci NULL,
			   PRIMARY KEY (`pk`)
			) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",

			"gb_triggers" => "CREATE TABLE IF NOT EXISTS `".$prefix."gb_triggers` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`name` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
			`type` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
			`criterion` text COLLATE utf8_unicode_ci,
			`target` text COLLATE utf8_unicode_ci,
			`status` int(1) DEFAULT NULL,
			`created` timestamp NULL DEFAULT NULL,
			`modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`)
			)  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
			
			"gb_config" => "CREATE TABLE IF NOT EXISTS `".$prefix."gb_config` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `key` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
			  `value` longtext COLLATE utf8_unicode_ci NOT NULL,
			  PRIMARY KEY (`id`)
			) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",

            "gb_groups" => "CREATE TABLE IF NOT EXISTS `".$prefix."gb_groups` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
			  `type` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `remote_id` int(11) DEFAULT NULL,
			  `group_leaders` text COLLATE utf8_unicode_ci,
			  `created` timestamp NULL DEFAULT NULL,
			  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`)
            ) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",

            "gb_group_agents" => "CREATE TABLE IF NOT EXISTS `".$prefix."gb_group_agents` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
			  `group_id` int(11) NOT NULL,
			  `agent_id` varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
			  `created` timestamp NULL DEFAULT NULL,
			  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`)
            ) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",

			"gb_contents" =>	"CREATE TABLE `".$prefix."gb_contents` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `type` int(5) NOT NULL,
			  `remote` int(13) NOT NULL,
			  `parent` int(5) NOT NULL,
			  `objectid` varchar(2048) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `status` int(1) NOT NULL DEFAULT '1',
			  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`)
			) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",

			"gb_content_types" => "CREATE TABLE `".$prefix."gb_content_types` (
			  `id` int(5) NOT NULL AUTO_INCREMENT,
			  `name` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
			  `status` int(1) DEFAULT '1',
			  `levels` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `parents` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			INSERT INTO `".$prefix."gb_content_types` (`id`, `name`, `status`, `levels`, `parents`) VALUES
				(1, 'Course', 1, '1', ''),
				(2, 'Lesson', 1, '2', '1'),
				(3, 'Topic', 1, '3', '2'),
				(4, 'Quiz', 1, '3,4', '2,3'),
				(5, 'xAPI Content', 1, '', '');
			", 
			"gb_error_log"	=> "CREATE TABLE `".$prefix."gb_error_log` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `type` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'trigger/statement/state',
			  `user` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `objectid` varchar(2048) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `statement_id` varchar(36) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `url` text COLLATE utf8_unicode_ci,
			  `request_method` varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `data` text COLLATE utf8_unicode_ci,
			  `error_msg` text COLLATE utf8_unicode_ci,
			  `error_code` int(4) NOT NULL DEFAULT '0',
			  `response` text COLLATE utf8_unicode_ci DEFAULT NULL,
			  `status` int(1) NOT NULL,
			  `IP` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
			  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  `modified` timestamp NULL DEFAULT NULL,
			 PRIMARY KEY (`id`)
			) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
			);


					clear_cache();
			foreach($gb_tables as $gb_table) {
				if(!in_array($prefix.$gb_table, $tables)) {
					echo "<div class='notice'>".__("Created table:").$prefix.$gb_table."</div>";

					clear_cache();
					echo "<pre style='display:none;'>".$gb_tables_sql[$gb_table]."</pre>";
					$this->execute($gb_tables_sql[$gb_table]);
					if($gb_table == "gb_triggers")
					$this->upgradeTables("1.0.2");
					clear_cache();
				}
			}
		//$this->_execute($sql);
	}
	function loadxmls($urls = array()) {
		if(isset($_GET["debug"]))
			{echo "<pre>"; print_r($urls); echo "</pre>";}
		try {
			if(empty($urls)) {
				$prefix = $this->db->config["prefix"];
				if($this->table_exists("postmeta")) {
					$sql = "SELECT post_id, meta_value FROM ".$prefix."postmeta WHERE meta_key = 'xapi_content'";
					$result = $this->execute($sql);
					clear_cache();
					$count = 0;

					while($data = $result->fetch()) {
						$xapi_content = unserialize($data->meta_value);
							if(isset($_GET["debug"]))
								{echo "<pre>"; print_r($xapi_content); echo "</pre>";}
								
							if(!empty($xapi_content["src"])) {
							$xml_url = dirname($xapi_content["src"])."/tincan.xml";
							if(isset($_GET["debug"]))
							echo "<pre>".$xml_url."</pre>";

                            $xml = file_get_contents_curl($xml_url);
							if(isset($_GET["debug"]))
							echo "<pre>".$xml."</pre>";
							$ids = array();
							$post = $this->get_post($data->post_id);
							$description = "";
							if(!empty($xml)) {
								
									$tincanxml = simplexml_load_string($xml);

									if(isset($_GET["debug"]))
									{echo "<pre>"; print_r($tincanxml); echo "</pre>";}
									//print_r($tincanxml);
									$ids  = $this->get_xml_array($tincanxml);
									if(isset($_GET["debug"]))
									{echo "<pre>"; print_r($ids); echo "</pre>";}
									
									if(!empty($ids))
									foreach ($ids as $key => $value) {
										if($value["id"] == $xapi_content["activity_id"])
											$description = "";
										else
										$count += $this->update_translation($value["id"], $value["name"], $value["description"]);
									}
							}

							if(!empty($xapi_content["activity_id"]) && !empty($post->post_title))
							$count += $this->update_translation($xapi_content["activity_id"], $post->post_title, $description);
						}
					}
					return "<div class='notice'>".sprintf(__("Loaded %s ids from xml"), $count)."</div>";
				}
			}
			else
			{ 
				$count = 0;
				if(!is_array($urls))
					$urls = array($urls);

				foreach($urls as $url) {
					if(!empty($url)) {
						$xml = file_get_contents_curl($url);
						$ids = array();
						if(!empty($xml)) {
							$tincanxml = simplexml_load_string($xml);

							//print_r($tincanxml);
							$ids  = $this->get_xml_array($tincanxml);

							if(!empty($ids))
							foreach ($ids as $key => $value) {
								$count += $this->update_translation($value["id"], $value["name"], $value["description"]);
							}
						}
					}
				}
				return "<div class='notice'>".sprintf(__("Loaded %s ids from xml"), $count)."</div>";
			}
		}
		catch (Exception $e)
		{
			if(isset($_GET["debug"]))
			{echo "<pre>Error: ".$e->getMessage()."</pre>";}
		}		
	}
	function get_post($conditions) {
		if(!$this->table_exists("posts"))
			return;

		if(is_numeric($conditions))
			$conditions = array("ID" => $conditions);

		foreach ($conditions as $key => $value) {
			$conditions[$key] = $this->db->name($key)." = ".$this->db->value($value); 
		}
		$sql = "SELECT * FROM ".$this->db->config["prefix"]."posts WHERE ".implode(" AND ", $conditions)." LIMIT 1";
		$result = $this->execute($sql);
		return $this->clean_result($result->fetch());
	}
	function table_exists($table) {
		$prefix = $this->db->config["prefix"];
		$tables = $this->db->listSources();
		if(in_array($prefix.$table, $tables)) 
			return true;
		else
			return false;
	}
	function clean_result($result) {
		if(!empty($result))
		{
			$ret = array();
			foreach($result as $k => $v) {
			if($k != "queryString")
				$ret[$k] = $v;
			}
			return (object) $ret;
		}
		return;
	}
	function update_translation($id, $name, $description) {
		$count = 0;
		$translation = $this->get_translation($id);
		if(empty($translation)) {
			$sql = "INSERT INTO ".$this->db->config["prefix"]."gb_id_translations (id, name, description) VALUES (".$this->value($id).", ".$this->value($name).", ".$this->value($description).")";
			$this->execute($sql);
			$count++;
		}
		else if($translation->name != $name || $translation->description != $description) {
			$sql = "UPDATE ".$this->db->config["prefix"]."gb_id_translations 
					SET
					name = ".$this->value($name).", 
					description = ".$this->value($description)."
					WHERE id = ".$this->value($id)."
					LIMIT 1
					";
			$this->execute($sql);			
			$count++;
		}
		return $count;
	}
	function get_translation($id) {
		$id = str_replace("urn:dummy:","",$id);
		$sql = "SELECT * FROM ".$this->db->config["prefix"]."gb_id_translations WHERE id = ".$this->value($id)." LIMIT 1";
		$result = $this->execute($sql);
		return $result->fetch();
	}
	function get_in_statement_translation($id, $statement) {
		if(is_string($statement))
			$statement = json_decode($statement);

		if(empty($statement))
			return null;

		if(!empty($statement->{"object"}) && !empty($statement->{"object"}->id) && $statement->{"object"}->id == $id && !empty($statement->{"object"}->definition))
		{
			$definition = $statement->{"object"}->definition;
			if(!empty($definition->{"name"}) && is_object($definition->{"name"}))
			{
				if(!empty($definition->{"name"}->{"en-US"}) && is_string($definition->{"name"}->{"en-US"}))
					return $definition->{"name"}->{"en-US"};

				$name = reset($definition->{"name"});
				if(is_string($name))
					return $name;
			}
			if(!empty($definition->description) && is_object($definition->description))
			{
				if(!empty($definition->description->{"en-US"}) && is_string($definition->description->{"en-US"}))
					return $definition->description->{"en-US"};

				$description = reset($definition->description);
				if(is_string($description))
					return $description;
			}
		}

		$choices_types = array("choices","scale","source","target", "steps");
		foreach ($choices_types as $choices_type) {
			if(!empty($statement->{"object"}) && !empty($statement->{"object"}->definition) && !empty($statement->{"object"}->definition->{$choices_type}) && is_array($statement->{"object"}->definition->{$choices_type}))
			{
				foreach ($statement->{"object"}->definition->{$choices_type} as $choice) {
					if(!empty($choice->id) && $choice->id == $id && !empty($choice->description) && is_object($choice->description))
					{
						if(!empty($choice->description->{"en-US"}) && is_string($choice->description->{"en-US"}))
							return $choice->description->{"en-US"};
						$description = reset($choice->description);
						if(is_string($description))
							return $description;
					}
				}
			}
		}	
	}
	function get_translation_name($id, $return_id = false, $statement = null) {
		$id = str_replace("urn:dummy:","",$id);
		
		$in_statement = $this->get_in_statement_translation($id, $statement);
		if(!empty($in_statement))
			return $in_statement;

		$sql = "SELECT * FROM ".$this->db->config["prefix"]."gb_id_translations WHERE id = ".$this->value($id)." LIMIT 1";
		$result = $this->execute($sql);
		$data = $result->fetch();
		$name = "";
		if(!empty($data->name))
		$name = $data->name;
		if(empty($name) && $return_id) {
			$name = $id;
		}
		return $name;
	}
	function get_translation_description($id, $return_id = false) {
		$id = str_replace("urn:dummy:","",$id);
		$sql = "SELECT * FROM ".$this->db->config["prefix"]."gb_id_translations WHERE id = ".$this->value($id)." LIMIT 1";
		$result = $this->execute($sql);
		$data = $result->fetch();
		$description = "";
		if(!empty($data->description))
		$description = $data->description;
		
		if(empty($description) && $return_id) {
			$description = $id;
		}
		return $description;
	}
	function get_response_translation_name($id, $return_id = false, $separator = "<br>", $statement = null) {
		if(!is_string($id))
		return "";

		$responses = explode("[,]", $id);
		$responses_names = array();
		foreach($responses as $response) {
			$response_parts = explode("[.]", $response);
			$response_parts_names = array();
			foreach ($response_parts as $key => $r) {
				$response_parts_names[] = $this->get_translation_name($r, $return_id, $statement);
			}
			$responses_names[] = implode(" = ", $response_parts_names);
		}
		return implode($separator, $responses_names);
	}
	function get_xml_array($xmlobj) {
		if(!empty($xmlobj["id"]) && !empty($xmlobj->name)) {
			$id = (string) $xmlobj["id"];
			$name = (string) $xmlobj->name;

			$return = array();

			if(!empty($id) && !empty($name)) {
			$description = !empty($xmlobj->description)? (string) $xmlobj->description:"";
			$return[] = array(
					"id" => $id,
					"name" => $name,
					"description" => $description,
				);
			}
			foreach ($xmlobj as $key => $value) {
				if((is_array($value) || is_object($value))) {
					$return_arr = $this->get_xml_array($value);
					if(!empty($return_arr["id"]))
						$return[] = $return_arr;
					else if(!empty($return_arr[0]))
						foreach ($return_arr as $k => $v) {
							$return[] = $v;
						}
				}	
			}
			if(count($return) == 1)
				return $return[0];
			else
			return $return;
		}
		else
		if(!empty($xmlobj->id) && !empty($xmlobj->description)) {

			$id = (string) $xmlobj->id;
			$description = $name = (string) $xmlobj->description;

			$return = array();

			if(!empty($id) && !empty($name)) {
			$return[] = array(
					"id" => $id,
					"name" => $name,
					"description" => $description,
				);
			}
			foreach ($xmlobj as $key => $value) {
				if((is_array($value) || is_object($value))) {
					$return_arr = $this->get_xml_array($value);
					if(!empty($return_arr["id"]))
						$return[] = $return_arr;
					else if(!empty($return_arr[0]))
						foreach ($return_arr as $k => $v) {
							$return[] = $v;
						}
				}	
			}
			if(count($return) == 1)
				return $return[0];
			else
			return $return;
		
		}
		else
		{
			$return = array();
			foreach ($xmlobj as $key => $value) {
				if((is_array($value) || is_object($value))) {
					$return_arr = $this->get_xml_array($value);
					if(!empty($return_arr["id"]))
						$return[] = $return_arr;
					else if(!empty($return_arr[0]))
						foreach ($return_arr as $k => $v) {
							$return[] = $v;
						}
				}	
			}
			return $return;
		}
	}
}
