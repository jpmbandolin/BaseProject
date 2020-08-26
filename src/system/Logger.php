<?php


namespace system;

use Monolog\Handler\StreamHandler;
use system\exceptions\RuntimeException;

/**
 * Class Logger
 * @see https://github.com/Seldaek/monolog/blob/master/doc/
 */
class Logger extends \Monolog\Logger{

	/**
	 * @var null
	 */
	private static $requestID		= null;

	/**
	 * @return string|null
	 */
	private static function getRequestID(){
		if(self::$requestID === NULL){
			self::$requestID = uniqid("", true);
		}
		return self::$requestID;
	}

	/**
	 * Logger constructor.
	 *
	 * @param       $name
	 * @param array $handlers
	 * @param array $processors
	 *
	 * @throws RuntimeException
	 */
	public function __construct($name, array $handlers = [], array $processors = []){
		global $ENV;
		parent::__construct($name, $handlers, $processors);

		$loggerDirectory = __DIR__ . $ENV['LOGGER']['filepath'].$name . ".log";

		try{
			$logLevel = $ENV['LOGGER']['log_level'];
			$streamHandler = new StreamHandler($loggerDirectory, constant("self::$logLevel"));
		}catch(\Throwable $t){
			throw new RuntimeException("Erro ao inicializar o logger", 0, $t);
		}

		self::pushHandler($streamHandler);

		self::pushProcessor(function ($entry) {
			$entry['extra']['requestId'] = self::getRequestID();
			return $entry;
		});
	}

}