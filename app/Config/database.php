<?php

class DATABASE_CONFIG
{
	public $default = array(
		'datasource' => 'Database/Mysql',
		'persistent' => false,
		'host' => SANDBOX_DB_HOST,
		'login' => SANDBOX_DB_USERNAME,
		'password' => SANDBOX_DB_PASSWORD,
		'database' => SANDBOX_DB_NAME
	);
}
