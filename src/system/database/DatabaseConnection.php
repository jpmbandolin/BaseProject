<?php


namespace system\database;


class DatabaseConnection extends DatabaseConnectionAbstract{
	/**
	 * @var DatabaseConnection
	 */
	private static $instance = null;

	/**
	 * @return DatabaseConnection
	 */
	public static function getInstance(){
		if(self::$instance === null){
			self::$instance = new self;
		}

		return self::$instance;
	}
}