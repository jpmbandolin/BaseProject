<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require_once __DIR__ . '/vendor/autoload.php';
$ENV = parse_ini_file(__DIR__ . "/env.ini", true);

set_error_handler(function($errno, $errstr, $errfile, $errline){
	$logger = new \system\Logger("index");
	$logger->error("Erro capturado pelo sistema", [
		"errno"=>$errno,
		"errstr"=>$errstr,
		"errfile"=>$errfile,
		"errline"=>$errline
	]);
});

$c = new \Slim\Container();
$c['errorHandler'] = function($c){
	return function (Request $request, Response $response, Throwable $exception) {
		if(!is_subclass_of($exception, "\system\\exceptions\AppExceptionAbstract")){
			$runtimeException = new \system\exceptions\RuntimeException("Erro na resposta do servidor", 0, $exception);
			$message = $runtimeException->getMessage();
		}else{
			$message = $exception->getMessage();
		}

		return $response->withJson(["error"=>$message], 500);
	};
};

$app = new \Slim\App($c);

$app->group('/usuario', '\usuario\Router::routes');

$app->run();



