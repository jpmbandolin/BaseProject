<?php
namespace system\database;

use Monolog\Logger;
use system\exceptions\DatabaseException;

abstract class DatabaseConnectionAbstract extends \PDO{
	/**
	 * @var string host para conexão com o banco
	 */
	private $host;
	/**
	 * @var string usuario para conexão com o banco
	 */
	private $user;
	/**
	 * @var string senha para conexão com o banco
	 */
	private $pass;
	/**
	 * @var Logger Responsável por registrar os logs da classe
	 */
	private $logger;

	/**
	 * DatabaseConnectionAbstract constructor.
	 * @throws DatabaseException
	 */
	function __construct(){
		global $ENV;

		//@todo criar a classe de logger

		if(is_a($this, __NAMESPACE__ . '\\' .'DatabaseConnection')){
			list($this->host, $this->user, $this->pass) = [$ENV['host'], $ENV['user'], $ENV['pass']];
		}else{
			throw new DatabaseException("Classe de conexão com o banco não implementada");
		}

		try{
			parent::__construct($this->host, $this->user, $this->pass, ["charset"=>"utf8"]);
		}catch(\Throwable $t){
			throw new DatabaseException("Erro ao iniciar conexão com o banco de dados", 0, $t);
		}
	}

	/**
	 * @param        $sql
	 * @param array  $args
	 * @param string $class_name
	 *
	 * @return mixed
	 */
	public function fetchObject($sql, $args = array(), $class_name = "stdClass"){
		$sql    = $this->prepareAndExecute($sql, $args);
		$object = $sql->fetchObject($class_name);
		$sql->closeCursor();
		return $object;
	}

	/**
	 * @param        $sql
	 * @param array  $args
	 * @param string $class_name
	 *
	 * @return array
	 */
	public function fetchMultiObject($sql, $args = array(), $class_name = "stdClass"){
		$sql    = $this->prepareAndExecute($sql, $args);
		$array  = $sql->fetchAll(self::FETCH_CLASS, $class_name);
		$sql->closeCursor();
		return $array;
	}

	/**
	 * @param       $sql
	 * @param array $args
	 *
	 * @return bool|\PDOStatement
	 */
	public function prepareAndExecute($sql, $args = array()){
		$sql = $this->prepare($sql);
		if(empty($args)){
			$sql->execute();
		} else{
			$sql->execute($args);
		}

		return $sql;
	}
}