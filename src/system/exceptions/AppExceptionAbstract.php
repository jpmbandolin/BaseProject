<?php


namespace system\exceptions;


use system\database\DatabaseConnection;
use system\Logger;
use Throwable;

abstract class AppExceptionAbstract extends \Exception{
	private $errorStack = [];

	public function __construct($message = "", $code = 0, Throwable $previous = null){
		parent::__construct($message, $code, $previous);

		$logger = new Logger(get_class($this));

		$logger->error($message, $this->getPreviousDetails());
		$this->saveDatabase();
	}

	private function getPreviousDetails(){
		$previousCount      = 0;
		$currentException   = $this->getPrevious();

		while($currentException !== null){
			$previousCount++;
			$this->errorStack = array_merge($this->errorStack, [
				"message[".$previousCount."]"=>$currentException->getMessage(),
				"line[".$previousCount."]"=>$currentException->getLine()
			]);

			$currentException = $currentException->getPrevious();
		}

		return $this->errorStack;
	}

	/**
	 * Salva os dados da exception no banco de dados.
	 * @return bool true caso tenha sucesso, false em caso de erro.
	 */
	protected function saveDatabase(){
		$sql = "INSERT INTO exception_logs (cFile, cTrace) VALUES(?, ?)";
		try{
			DatabaseConnection::getInstance()->prepareAndExecute($sql, [$this->getFile(), $this->errorStack]);
		}catch(Throwable $t){
			return false;
		}

		return true;
	}
}