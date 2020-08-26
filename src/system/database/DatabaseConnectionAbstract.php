<?php
namespace system\database;

use system\exceptions\DatabaseException;
use system\exceptions\RuntimeException;
use system\Logger;

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
	 * @var string database do sistema
	 */
	private $database;
	/**
	 * @var Logger Responsável por registrar os logs da classe
	 */
	private $logger;

	/**
	 * DatabaseConnectionAbstract constructor.
	 * @throws DatabaseException
	 * @throws RuntimeException
	 */
	function __construct(){
		global $ENV;
		$dbEnv = $ENV['DATABASE_CONNECTION'];
		$this->logger = new Logger(get_class($this));

		if(is_a($this, __NAMESPACE__ . '\\' .'DatabaseConnection')){
			[$this->host, $this->user, $this->pass, $this->database] = [$dbEnv['host'], $dbEnv['user'], $dbEnv['pass'], $dbEnv['database']];
		}else{
			throw new DatabaseException("Classe de conexão com o banco não implementada");
		}

		$dsn = 'mysql:dbname=' . $this->database .';host=' . $this->host;

		try{
			parent::__construct($dsn, $this->user, $this->pass, ["charset"=>"utf8"]);
			$this->exec("set names utf8");
		}catch(\Throwable $t){
			$this->logger->alert("Banco de dados não disponível");
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
		$this->logger->info($sql, ["params"=>json_encode($args)]);
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
		$this->logger->info($sql, ["params"=>json_encode($args)]);
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
		$this->logger->info($sql, ["params"=>json_encode($args)]);
		$sql = $this->prepare($sql);
		if(empty($args)){
			$sql->execute();
		} else{
			$sql->execute($args);
		}

		return $sql;
	}
}