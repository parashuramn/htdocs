<?php 
	class DATABASE_CONFIG {
		public $default = array(
			'datasource' => 'Database/Mysql',
			'persistent' => false,
			'host' => 'localhost',
			'login' => 'root',
			'password' => 'hunter1',
			'database' => 'grassblade',
			'prefix' => 'wp_',
			'encoding' => 'utf8',
			'settings'=> array('@@SESSION.sql_mode' => "(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))",),
		);
	}