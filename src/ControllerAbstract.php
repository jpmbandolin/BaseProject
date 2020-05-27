<?php


abstract class ControllerAbstract{
	/**
	 * @var \system\database\DatabaseConnection
	 */
	protected $con;

	public function __construct(){
		$this->con = \system\database\DatabaseConnection::getInstance();
	}
}